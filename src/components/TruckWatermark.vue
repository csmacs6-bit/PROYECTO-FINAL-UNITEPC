<template>
  <div class="truck-watermark" aria-hidden="true" />
</template>

<style scoped>
.truck-watermark {
  position: fixed;
  left: 240px;
  right: 0;
  bottom: 0;
  z-index: 0;
  height: 300px;
  background:
    url('/img/truck.jpg') center bottom / cover no-repeat,
    url('/img/truck-hero.png') center bottom / cover no-repeat;
  opacity: .07;
  pointer-events: none;
}

@media (max-width: 1023px) {
  .truck-watermark {
    left: 0;
    height: 220px;
  }
}
</style>
