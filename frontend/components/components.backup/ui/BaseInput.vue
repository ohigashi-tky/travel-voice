<template>
  <div class="space-y-1">
    <label
      v-if="label"
      :for="inputId"
      class="block text-sm font-medium text-gray-700"
    >
      {{ label }}
      <span
        v-if="required"
        class="text-red-500"
      >*</span>
    </label>
    
    <div class="relative">
      <input
        :id="inputId"
        v-model="inputValue"
        :type="type"
        :placeholder="placeholder"
        :required="required"
        :disabled="disabled"
        :class="inputClasses"
        @blur="$emit('blur')"
        @focus="$emit('focus')"
      >
      
      <Icon
        v-if="icon"
        :name="icon"
        class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400"
      />
    </div>
    
    <p
      v-if="error"
      class="text-sm text-red-600"
    >
      {{ error }}
    </p>
    
    <p
      v-if="hint && !error"
      class="text-sm text-gray-500"
    >
      {{ hint }}
    </p>
  </div>
</template>

<script setup lang="ts">
interface Props {
  modelValue: string
  type?: string
  label?: string
  placeholder?: string
  error?: string
  hint?: string
  icon?: string
  required?: boolean
  disabled?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  type: 'text',
  required: false,
  disabled: false
})

const emit = defineEmits<{
  'update:modelValue': [value: string]
  blur: []
  focus: []
}>()

const inputId = `input-${Math.random().toString(36).substr(2, 9)}`

const inputValue = computed({
  get: () => props.modelValue,
  set: (value: string) => emit('update:modelValue', value)
})

const inputClasses = computed(() => {
  const base = 'block w-full rounded-lg border-gray-300 shadow-sm transition-colors duration-200 focus:border-primary-500 focus:ring-primary-500 sm:text-sm'
  const iconPadding = props.icon ? 'pl-10' : 'px-3'
  const errorState = props.error ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : ''
  const disabledState = props.disabled ? 'bg-gray-50 cursor-not-allowed' : ''
  
  return [base, iconPadding, 'py-2', errorState, disabledState].filter(Boolean).join(' ')
})
</script>