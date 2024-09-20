<template>
  <div
    class="w-dvw h-dvh text-center"
  >
    <div
      v-if="state.loading"
      class="my-5 text-gray-300"
    >
      Loading...
    </div>
  </div>
</template>

<script setup>
import { state, resolveOneTimePassword } from '~/lib/one-time-password-login.js'

const authStore = useAuthStore()
const formsStore = useFormsStore()
const workspaceStore = useWorkspacesStore()

onBeforeMount(() => {
  resolveOneTimePassword(authStore, formsStore, workspaceStore)
})
</script>
