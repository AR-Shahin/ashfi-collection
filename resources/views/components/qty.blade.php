@props(['id', 'name' => 'qty', 'value' => 1, 'label' => 'Quantity'])
<div class="qty" data-qty>
  <button type="button" class="qty__btn" data-qty-dec aria-label="Decrease quantity"><x-ui.icon name="minus" :size="16" /></button>
  <input class="qty__input" id="{{ $id }}" name="{{ $name }}" type="text" inputmode="numeric" pattern="[0-9]*" value="{{ $value }}" aria-label="{{ $label }}" maxlength="2">
  <button type="button" class="qty__btn" data-qty-inc aria-label="Increase quantity"><x-ui.icon name="plus" :size="16" /></button>
</div>
