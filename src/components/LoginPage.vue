<script setup>
import { ref } from 'vue'

const emit = defineEmits(['success'])

const email = ref('healthatm@axissol.com')
const password = ref('password')
const error = ref(null)
const logoFailed = ref(false)
const logoUrl = new URL('../assets/axis-logo.jpeg', import.meta.url).href

function submit() {
  error.value = null

  if (email.value === 'healthatm@axissol.com' && password.value === 'password') {
    emit('success')
    return
  }

  error.value = 'Invalid email or password.'
}
</script>

<template>
  <div class="page">
    <div class="card">
      <img
        v-if="!logoFailed"
        class="logo"
        :src="logoUrl"
        alt="Axis Solutions"
        @error="logoFailed = true"
      />
      <div v-else class="logoFallback">Axis Solutions</div>

      <h1 class="title">Health ATM</h1>
      <p class="subtitle">Sign in to continue</p>

      <form class="form" @submit.prevent="submit">
        <label class="label" for="email">Email</label>
        <input id="email" v-model.trim="email" class="input" type="email" autocomplete="username" />

        <label class="label" for="password">Password</label>
        <input
          id="password"
          v-model="password"
          class="input"
          type="password"
          autocomplete="current-password"
        />

        <button class="btn" type="submit">Login</button>
        <p v-if="error" class="error">{{ error }}</p>
      </form>
    </div>
  </div>
</template>

<style scoped>
.page {
  min-height: 100vh;
  display: grid;
  place-items: center;
  padding: 24px;
  background: radial-gradient(900px 500px at 15% 20%, rgba(56, 189, 248, 0.35), transparent 60%),
    radial-gradient(900px 600px at 85% 25%, rgba(34, 211, 238, 0.28), transparent 55%),
    radial-gradient(1000px 700px at 50% 90%, rgba(14, 165, 233, 0.18), transparent 60%),
    linear-gradient(180deg, #eaf7ff 0%, #d6f1ff 45%, #c7ecff 100%);
  color: #0b1b2a;
}
.card {
  width: min(420px, 100%);
  border-radius: 16px;
  padding: 20px;
  border: 1px solid rgba(2, 132, 199, 0.18);
  background: rgba(255, 255, 255, 0.75);
  backdrop-filter: blur(10px);
  box-shadow:
    0 10px 30px rgba(2, 132, 199, 0.12),
    0 1px 0 rgba(255, 255, 255, 0.6) inset;
}
.logo {
  width: 200px;
  max-width: 100%;
  display: block;
  margin: 0 auto 14px;
  border-radius: 18px;
  background: #fff;
  padding: 8px;
  border: 1px solid rgba(255, 255, 255, 0.12);
}
.logoFallback {
  text-align: center;
  font-weight: 700;
  letter-spacing: 0.2px;
  opacity: 0.9;
  margin-bottom: 14px;
}
.title {
  text-align: center;
  font-size: 22px;
  margin: 0;
}
.subtitle {
  text-align: center;
  opacity: 0.75;
  margin: 6px 0 16px;
}
.form {
  display: grid;
  gap: 10px;
}
.label {
  font-size: 12px;
  opacity: 0.8;
}
.input {
  width: 100%;
  padding: 10px 12px;
  border-radius: 12px;
  border: 1px solid rgba(2, 132, 199, 0.25);
  background: rgba(255, 255, 255, 0.9);
  color: inherit;
}
.btn {
  margin-top: 6px;
  width: 100%;
  padding: 10px 12px;
  border-radius: 12px;
  border: 1px solid rgba(2, 132, 199, 0.35);
  background: rgba(2, 132, 199, 0.12);
  color: inherit;
  cursor: pointer;
}
.btn:hover {
  background: rgba(2, 132, 199, 0.18);
}
.btn:active {
  transform: translateY(1px);
}
.error {
  color: #b42318;
  margin: 4px 0 0;
}
</style>

