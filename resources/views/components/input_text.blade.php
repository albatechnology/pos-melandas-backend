<div class="form-group">
    <label class="required" for="{{$key}}">{{ $label }}</label>
    <input class="form-control {{ $errors->has($key) ? 'is-invalid' : '' }}"
           type="text" name="{{$key}}" id="{{$key}}" value="{{ old($key, $value) }}"
           required>
    @if($errors->has($key))
        <span class="text-danger">{{ $errors->first($key) }}</span>
    @endif
    <span class="help-block">{{ $label_helper }}</span>
</div>