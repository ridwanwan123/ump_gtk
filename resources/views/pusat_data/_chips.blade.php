{{--
    Kelompok pilihan berbentuk tombol (boleh pilih lebih dari satu).
    Tombol "Semua" aktif selama belum ada pilihan yang dicentang.

    $name     : nama input (dikirim sebagai array)
    $prefix   : awalan id, supaya unik antar form
    $options  : [nilai => label]
    $selected : nilai yang sedang terpilih
--}}
<div class="chip-group">
    <button type="button" class="chip chip-semua {{ empty($selected) ? 'aktif' : '' }}">
        <i class="fas fa-check"></i> Semua
    </button>

    @foreach ($options as $value => $label)
        <input type="checkbox" class="chip-input" name="{{ $name }}[]" value="{{ $value }}"
            id="{{ $prefix }}-{{ $loop->index }}" {{ in_array((string) $value, $selected, true) ? 'checked' : '' }}>
        <label class="chip" for="{{ $prefix }}-{{ $loop->index }}">
            <i class="fas fa-check"></i> {{ $label }}
        </label>
    @endforeach
</div>
