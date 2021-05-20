<div class="form-group">
    <label class="required" for="{{$key}}">{{ $label }}</label>
    <input class="form-control {{ $errors->has($key) ? 'is-invalid' : '' }}"
           type="number" name="{{$key}}" id="{{$key}}"
           value="{{$value}}" step="1" required>
    @if($errors->has($key))
        <span class="text-danger">{{ $errors->first($key) }}</span>
    @endif
    <span class="help-block">{{ $label_helper }}</span>
</div>