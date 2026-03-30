<script setup>
const props = defineProps({
  size: {
    type: String,
    default: "md",
    validator: (value) => ["sm", "md", "lg", "xl"].includes(value),
  },
  color: {
    type: String,
    default: "primary",
    validator: (value) => ["primary", "white", "gray"].includes(value),
  },
  inline: {
    type: Boolean,
    default: false,
  },
});

const sizeMap = {
  sm: "16px",
  md: "24px",
  lg: "32px",
  xl: "48px",
};

const colorMap = {
  primary: "var(--primary-600)",
  white: "#ffffff",
  gray: "var(--gray-400)",
};

const spinnerSize = sizeMap[props.size];
const spinnerColor = colorMap[props.color];
</script>

<template>
  <div
    class="spinner"
    :class="{ 'spinner-inline': inline }"
    :style="{
      width: spinnerSize,
      height: spinnerSize,
      borderColor: `${spinnerColor}33`,
      borderTopColor: spinnerColor,
    }"
  />
</template>

<style scoped>
.spinner {
  border-style: solid;
  border-width: 2px;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

.spinner-inline {
  display: inline-block;
  vertical-align: middle;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
