

@props(['areas', 'subareas', 'selectedArea' => '', 'selectedSubarea' => ''])
<div class="col-md-3">
    <div class="form-group">
        {{Form::label('area', 'Area')}}
        <select id="area" name="area" class="form-control" onchange="filtrarSubareas()">
            <option value="">Seleccionar...</option>
            @foreach ($areas as $area)
                <option value="{{ $area }}" {{ (old('area', $selectedArea ?? '') == $area) ? 'selected' : '' }}>
                    {{ $area }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<div class="col-md-3">
    <div class="form-group">
        {{Form::label('subarea', 'Subárea')}}
        <select id="subarea" name="subarea" class="form-control">
            <option value="">Seleccionar...</option>
        </select>
    </div>
</div>

<script>
    const subareas = @json($subareas);
    const selectedArea = "{{ old('area', $selectedArea ?? '') }}";
    const selectedSubarea = "{{ old('subarea', $selectedSubarea ?? '') }}";

    document.addEventListener('DOMContentLoaded', function () {
        filtrarSubareas(selectedSubarea);
    });

    function filtrarSubareas(preselectedSubarea = '') {
        const areaSelect = document.getElementById('area');
        const subareaSelect = document.getElementById('subarea');
        const areaSeleccionada = areaSelect.value.charAt(0);

        subareaSelect.innerHTML = '<option value="">Seleccionar...</option>';

        subareas.forEach(function(sub) {
            if (sub.charAt(0) === areaSeleccionada) {
                const option = document.createElement('option');
                option.value = sub;
                option.text = sub;

                if (sub === preselectedSubarea) {
                    option.selected = true;
                }

                subareaSelect.add(option);
            }
        });
    }
</script>
