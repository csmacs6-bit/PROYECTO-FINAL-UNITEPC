<template>
  <section class="brand-panel">
    <div class="brand-content">
      <h1>{{ title }}</h1>
      <p class="description">{{ description }}</p>
    </div>

    <div class="patriot-mark" aria-hidden="true">
      <span class="red"></span>
      <span class="yellow"></span>
      <span class="green"></span>
    </div>
  </section>
</template>

<script setup>
defineProps({
  title: {
    type: String,
    required: true,
  },
  description: {
    type: String,
    required: true,
  },
})
</script>

<style scoped>
.brand-panel {
  position: relative;
  display: flex;
  flex-direction: column;
  justify-content: center;
  overflow: hidden;
  min-height: 100vh;
  padding: 48px;
  color: white;
  background:
    linear-gradient(90deg, rgba(15, 23, 42, .96) 0%, rgba(15, 23, 42, .86) 42%, rgba(15, 23, 42, .28) 100%),
    linear-gradient(0deg, rgba(15, 23, 42, .68), rgba(15, 23, 42, .2)),
    url('/img/truck-hero.png') center / cover no-repeat,
    #0f172a;
}

.brand-panel::after {
  content: "";
  position: absolute;
  inset: 0;
  background:
    linear-gradient(90deg, rgba(15, 23, 42, .08), transparent 55%);
  pointer-events: none;
}

.brand-content,
.patriot-mark {
  position: relative;
  z-index: 1;
}

.brand-content {
  max-width: 460px;
}

.brand-content h1 {
  margin: 0 0 14px;
  font-size: clamp(2rem, 4vw, 3rem);
  line-height: 1.05;
  letter-spacing: 0;
}

.description {
  margin: 0;
  color: #dbeafe;
  font-size: 18px;
  font-weight: 700;
  line-height: 1.7;
}

.patriot-mark {
  display: flex;
  width: 170px;
  height: 12px;
  overflow: hidden;
  margin-top: 28px;
  border-radius: 999px;
  box-shadow: 0 10px 28px rgba(0, 0, 0, .24);
}

.patriot-mark span {
  flex: 1;
}

.patriot-mark .red {
  background: #d52b1e;
}

.patriot-mark .yellow {
  background: #f9e300;
}

.patriot-mark .green {
  background: #007a33;
}

@media (max-width: 880px) {
  .brand-panel {
    min-height: 290px;
    padding: 24px;
  }
}
</style>
