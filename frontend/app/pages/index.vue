<script setup lang="ts">
import { resolveApiErrorMessage } from '~/api/errors'
import { healthQuery } from '~/api/health/queries'

const { state, asyncStatus, refetch } = useQuery(healthQuery)

const serviceLabels = {
  database: 'PostgreSQL',
  cache: 'Redis',
} as const
</script>

<template>
  <main class="p-home">
    <h1>Состояние бэкенда</h1>

    <p v-if="state.status === 'pending'">Проверяем…</p>

    <p
      v-else-if="state.status === 'error'"
      class="p-home__error"
    >
      {{ resolveApiErrorMessage(state.error, 'Бэкенд недоступен') }}
    </p>

    <template v-else>
      <p>{{ state.data.isHealthy ? 'Всё работает' : 'Есть сбои' }}</p>
      <ul>
        <li
          v-for="(label, service) in serviceLabels"
          :key="service"
        >
          {{ label }}: {{ state.data.services[service] ? 'ok' : 'сбой' }}
        </li>
      </ul>
    </template>

    <button
      type="button"
      :disabled="asyncStatus === 'loading'"
      @click="refetch()"
    >
      Проверить ещё раз
    </button>
  </main>
</template>
