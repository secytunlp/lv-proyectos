

<?php $attributes = $attributes->exceptProps(['areas', 'subareas', 'selectedArea' => '', 'selectedSubarea' => '']); ?>
<?php foreach (array_filter((['areas', 'subareas', 'selectedArea' => '', 'selectedSubarea' => '']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
} ?>
<?php $__defined_vars = get_defined_vars(); ?>
<?php foreach ($attributes as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
} ?>
<?php unset($__defined_vars); ?>
<div class="col-md-3">
    <div class="form-group">
        <?php echo e(Form::label('area', 'Area')); ?>

        <select id="area" name="area" class="form-control" onchange="filtrarSubareas()">
            <option value="">Seleccionar...</option>
            <?php $__currentLoopData = $areas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $area): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($area); ?>" <?php echo e((old('area', $selectedArea ?? '') == $area) ? 'selected' : ''); ?>>
                    <?php echo e($area); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </div>
</div>

<div class="col-md-3">
    <div class="form-group">
        <?php echo e(Form::label('subarea', 'Subárea')); ?>

        <select id="subarea" name="subarea" class="form-control">
            <option value="">Seleccionar...</option>
        </select>
    </div>
</div>

<script>
    const subareas = <?php echo json_encode($subareas, 15, 512) ?>;
    const selectedArea = "<?php echo e(old('area', $selectedArea ?? '')); ?>";
    const selectedSubarea = "<?php echo e(old('subarea', $selectedSubarea ?? '')); ?>";

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
<?php /**PATH /var/www/sicadi/resources/views/components/select-areas.blade.php ENDPATH**/ ?>