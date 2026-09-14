<?php

namespace App\Traits;

use App\Constants;
use Carbon\Carbon;

/**
 * Antigüedad en investigación de un solicitante de Jóvenes Investigadores.
 *
 * La convocatoria pide un mínimo de participación en proyectos UNLP / beca UNLP
 * (YEAR_PROYECTOS años). El cálculo original sumaba el período nominal completo de
 * cada beca y cada proyecto, así que contaba tiempo que todavía no transcurrió:
 * joven_proyectos.hasta es la fecha de fin del proyecto cuando el integrante no
 * tiene baja, y una beca doctoral se carga con sus cinco años. Alguien que acababa
 * de empezar sumaba siete u ocho años.
 *
 * Acá se corrigen las dos cosas:
 *   - cada intervalo se recorta en la fecha de corte (el cierre de la convocatoria);
 *     lo posterior no cuenta, y lo que empieza después del cierre no cuenta nada;
 *   - los intervalos se UNEN en vez de sumarse, así una beca y un proyecto que
 *     corren en paralelo cuentan una sola vez.
 */
trait CalculaAntiguedadJovenes
{
    /**
     * Días que hay que acreditar. YEAR_PROYECTOS años de DIAS_YEAR días.
     *
     * @return int
     */
    protected function diasMinimosAntiguedadJoven()
    {
        return intval(Constants::YEAR_PROYECTOS) * intval(Constants::DIAS_YEAR);
    }

    /**
     * Fecha hasta la que se computa la antigüedad: el cierre de la convocatoria,
     * el mismo criterio que usan las validaciones de beca y carrera de investigación.
     *
     * @return \Carbon\Carbon
     */
    protected function fechaCorteAntiguedadJoven()
    {
        return Carbon::parse(Constants::CIERRE_JOVENES)->startOfDay();
    }

    /**
     * Normaliza un par desde/hasta y lo recorta en la fecha de corte.
     *
     * Devuelve null cuando el intervalo no aporta nada: fechas vacías o inválidas,
     * hasta anterior a desde, o desde posterior al corte.
     *
     * @param  mixed  $desde
     * @param  mixed  $hasta
     * @param  \Carbon\Carbon  $corte
     * @return array|null
     */
    protected function intervaloAntiguedadJoven($desde, $hasta, Carbon $corte)
    {
        $desdeTxt = substr((string) $desde, 0, 10);
        $hastaTxt = substr((string) $hasta, 0, 10);

        if ($desdeTxt === '' || $hastaTxt === '') {
            return null;
        }
        if (strpos($desdeTxt, '0000-00-00') === 0 || strpos($hastaTxt, '0000-00-00') === 0) {
            return null;
        }

        try {
            $fechaDesde = Carbon::parse($desdeTxt)->startOfDay();
            $fechaHasta = Carbon::parse($hastaTxt)->startOfDay();
        } catch (\Exception $e) {
            return null;
        }

        if ($fechaHasta->lessThan($fechaDesde)) {
            return null;
        }
        if ($fechaDesde->greaterThan($corte)) {
            return null;
        }

        $recortado = $fechaHasta->greaterThan($corte);
        if ($recortado) {
            $fechaHasta = $corte->copy();
        }

        return [
            'desde'           => $fechaDesde,
            'hasta'           => $fechaHasta,
            'hasta_declarado' => $hastaTxt,
            'recortado'       => $recortado,
            'dias'            => $fechaDesde->diffInDays($fechaHasta),
            'origen'          => '',
            'detalle'         => '',
        ];
    }

    /**
     * Intervalos que computan: becas UNLP declaradas y proyectos declarados.
     *
     * Mismo conjunto de filas que miraba el cálculo original (joven_becas con
     * unlp = 1 y todos los joven_proyectos); lo que cambia es cómo se miden.
     *
     * @param  \App\Models\Joven  $solicitud
     * @param  \Carbon\Carbon|null  $corte
     * @return array
     */
    protected function intervalosAntiguedadJoven($solicitud, Carbon $corte = null)
    {
        if ($corte === null) {
            $corte = $this->fechaCorteAntiguedadJoven();
        }

        $intervalos = [];

        foreach ($solicitud->becas as $beca) {
            if (!$beca->unlp) {
                continue;
            }
            $intervalo = $this->intervaloAntiguedadJoven($beca->desde, $beca->hasta, $corte);
            if ($intervalo === null) {
                continue;
            }
            $intervalo['origen']  = 'Beca UNLP';
            $intervalo['detalle'] = trim((string) $beca->beca);
            $intervalos[] = $intervalo;
        }

        foreach ($solicitud->proyectos as $proyecto) {
            $intervalo = $this->intervaloAntiguedadJoven($proyecto->desde, $proyecto->hasta, $corte);
            if ($intervalo === null) {
                continue;
            }
            $intervalo['origen']  = 'Proyecto';
            $intervalo['detalle'] = trim((string) $proyecto->codigo);
            $intervalos[] = $intervalo;
        }

        return $intervalos;
    }

    /**
     * Une los intervalos superpuestos o contiguos.
     *
     * Dos intervalos se funden cuando el segundo arranca antes del día siguiente al
     * fin del primero, así una beca que termina el 31/12 y un proyecto que empieza
     * el 1/1 se cuentan como un tramo continuo.
     *
     * @param  array  $intervalos
     * @return array
     */
    protected function unirIntervalosAntiguedadJoven(array $intervalos)
    {
        if (empty($intervalos)) {
            return [];
        }

        usort($intervalos, function ($a, $b) {
            if ($a['desde']->equalTo($b['desde'])) {
                return $a['hasta']->lessThan($b['hasta']) ? -1 : 1;
            }
            return $a['desde']->lessThan($b['desde']) ? -1 : 1;
        });

        $unidos = [];
        $actual = null;

        foreach ($intervalos as $intervalo) {
            if ($actual === null) {
                $actual = [
                    'desde' => $intervalo['desde']->copy(),
                    'hasta' => $intervalo['hasta']->copy(),
                ];
                continue;
            }

            if ($intervalo['desde']->lessThanOrEqualTo($actual['hasta']->copy()->addDay())) {
                if ($intervalo['hasta']->greaterThan($actual['hasta'])) {
                    $actual['hasta'] = $intervalo['hasta']->copy();
                }
                continue;
            }

            $unidos[] = $actual;
            $actual = [
                'desde' => $intervalo['desde']->copy(),
                'hasta' => $intervalo['hasta']->copy(),
            ];
        }

        if ($actual !== null) {
            $unidos[] = $actual;
        }

        return $unidos;
    }

    /**
     * Días de antigüedad efectivos: unión de los intervalos recortados en el corte.
     *
     * @param  \App\Models\Joven  $solicitud
     * @param  \Carbon\Carbon|null  $corte
     * @return int
     */
    protected function diasAntiguedadJoven($solicitud, Carbon $corte = null)
    {
        $unidos = $this->unirIntervalosAntiguedadJoven(
            $this->intervalosAntiguedadJoven($solicitud, $corte)
        );

        $dias = 0;
        foreach ($unidos as $tramo) {
            $dias += $tramo['desde']->diffInDays($tramo['hasta']);
        }

        return $dias;
    }

    /**
     * Cálculo original, sin recortar y sumando períodos simultáneos.
     *
     * Se conserva sólo para que la auditoría pueda mostrar qué antigüedad computaba
     * antes cada solicitud, y así distinguir las que se enviaron por este bug de las
     * que nunca llegaron a cumplir. No se usa para validar.
     *
     * @param  \App\Models\Joven  $solicitud
     * @return int
     */
    protected function diasAntiguedadJovenCalculoAnterior($solicitud)
    {
        $dias = 0;

        foreach ($solicitud->becas as $beca) {
            if (!$beca->unlp) {
                continue;
            }
            $desde = substr((string) $beca->desde, 0, 10);
            $hasta = substr((string) $beca->hasta, 0, 10);
            if ($desde === '' || $hasta === '') {
                continue;
            }
            $dias += Carbon::parse($hasta)->diffInDays(Carbon::parse($desde));
        }

        foreach ($solicitud->proyectos as $proyecto) {
            $desde = substr((string) $proyecto->desde, 0, 10);
            $hasta = substr((string) $proyecto->hasta, 0, 10);
            if ($desde === '' || $hasta === '') {
                continue;
            }
            $dias += Carbon::parse($hasta)->diffInDays(Carbon::parse($desde));
        }

        return $dias;
    }

    /**
     * Texto legible de los intervalos computados, para los informes.
     *
     * @param  array  $intervalos
     * @return string
     */
    protected function describirIntervalosAntiguedadJoven(array $intervalos)
    {
        $partes = [];
        foreach ($intervalos as $intervalo) {
            $texto = $intervalo['origen'];
            if ($intervalo['detalle'] !== '') {
                $texto .= ' ' . $intervalo['detalle'];
            }
            $texto .= ': ' . $intervalo['desde']->format('d/m/Y') . ' - ' . $intervalo['hasta']->format('d/m/Y');
            if ($intervalo['recortado']) {
                $texto .= ' (declarado hasta ' . Carbon::parse($intervalo['hasta_declarado'])->format('d/m/Y') . ')';
            }
            $texto .= ' = ' . $intervalo['dias'] . ' d';
            $partes[] = $texto;
        }

        return implode(' | ', $partes);
    }
}
