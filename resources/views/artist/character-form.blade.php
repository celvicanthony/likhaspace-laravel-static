<div class="row g-3">
    <div class="col-12 col-md-6">
        <label class="form-label" for="character-title">Character title</label>
        <input id="character-title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title', $artwork->title ?? '') }}" maxlength="120" required>
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="character-genre">Genre / category</label>
        <select id="character-genre" name="genre" class="form-select @error('genre') is-invalid @enderror" required>
            <option value="">Choose category</option>
            @foreach ($genres as $genre)
                <option value="{{ $genre }}" @selected(old('genre', $artwork->genre ?? '') === $genre)>{{ $genre }}</option>
            @endforeach
        </select>
        @error('genre')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-4">
        <label class="form-label" for="license-type">License type</label>
        <select id="license-type" name="license_type" class="form-select @error('license_type') is-invalid @enderror" required>
            <option value="personal_use" @selected(old('license_type', $artwork->license_type ?? '') === 'personal_use')>Personal Use</option>
            <option value="commercial_use" @selected(old('license_type', $artwork->license_type ?? '') === 'commercial_use')>Commercial Use</option>
        </select>
        @error('license_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-4">
        <label class="form-label" for="file-format">Asset file format</label>
        <select id="file-format" name="file_format" class="form-select @error('file_format') is-invalid @enderror" required>
            @foreach (['PNG', 'PSD', 'ZIP'] as $format)
                <option value="{{ $format }}" @selected(old('file_format', $artwork->file_format ?? 'PNG') === $format)>{{ $format }}</option>
            @endforeach
        </select>
        @error('file_format')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-4">
        <label class="form-label" for="character-price">Price (PHP)</label>
        <input id="character-price" name="price" type="number" min="1" max="1000000" step="0.01" class="form-control @error('price') is-invalid @enderror" value="{{ old('price', $artwork->price ?? '') }}" required>
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="stock-type">Stock type</label>
        <select id="stock-type" name="stock_type" class="form-select @error('stock_type') is-invalid @enderror" required>
            <option value="single_adoptable" @selected(old('stock_type', $artwork->stock_type ?? '') === 'single_adoptable')>Single Adoptable (exclusive, one copy)</option>
            <option value="multi_copy" @selected(old('stock_type', $artwork->stock_type ?? 'multi_copy') === 'multi_copy')>Multi-Copy Asset</option>
        </select>
        @error('stock_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6" data-stock-quantity>
        <label class="form-label" for="stock-quantity">Available copies</label>
        <input id="stock-quantity" name="stock_quantity" type="number" min="1" max="9999" class="form-control @error('stock_quantity') is-invalid @enderror" value="{{ old('stock_quantity', $artwork->stock_quantity ?? 1) }}">
        @error('stock_quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text text-muted">Single adoptables are automatically limited to one copy.</div>
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="character-country">Culture / origin</label>
        <select id="character-country" name="country" class="form-select @error('country') is-invalid @enderror" required>
            @foreach (['Philippines', 'Greece', 'Scotland', 'Japan'] as $country)
                <option value="{{ $country }}" @selected(old('country', $artwork->country ?? 'Philippines') === $country)>{{ $country }}</option>
            @endforeach
        </select>
        @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="character-description">Description</label>
        <textarea id="character-description" name="description" rows="4" maxlength="2000" class="form-control @error('description') is-invalid @enderror">{{ old('description', $artwork->description ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="art-file">High-resolution asset (PNG, PSD, ZIP; up to 100 MB)</label>
        <input id="art-file" name="art_file" type="file" accept=".png,.psd,.zip" class="form-control @error('art_file') is-invalid @enderror" @required(!isset($artwork))>
        @error('art_file')<div class="invalid-feedback">{{ $message }}</div>@enderror
        @isset($artwork)<div class="form-text text-muted">Leave empty to keep the current private asset.</div>@endisset
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="preview-image">Watermarked preview image (JPG, PNG, WebP; up to 5 MB)</label>
        <input id="preview-image" name="preview_image" type="file" accept="image/png,image/jpeg,image/webp" class="form-control @error('preview_image') is-invalid @enderror" @required(!isset($artwork))>
        @error('preview_image')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text text-muted">Upload a preview with your watermark already applied. The asset itself stays private.</div>
    </div>
</div>
@push('scripts')
<script>
    (() => {
        const type = document.getElementById('stock-type');
        const quantity = document.querySelector('[data-stock-quantity]');
        const input = document.getElementById('stock-quantity');
        const updateStock = () => {
            const single = type.value === 'single_adoptable';
            quantity.classList.toggle('d-none', single);
            input.disabled = single;
        };
        type.addEventListener('change', updateStock);
        updateStock();
    })();
</script>
@endpush
