<template>
  <div>
    <!--  Login modal  -->
    <modal
      :show="showLoginModal"
      max-width="lg"
      @close="showLoginModal = false"
    >
      <template #icon>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="w-8 h-8"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"
          />
        </svg>
      </template>
      <template #title>
        Login to {{ runtimeConfig.public.appName }}
      </template>
      <div class="px-4">
        <login-form
          :is-quick="true"
          @open-register="openRegister"
          @after-quick-login="afterQuickLogin"
        />
      </div>
    </modal>

    <!--  Register modal  -->
    <modal
      :show="showRegisterModal"
      max-width="lg"
      @close="$emit('close')"
    >
      <template #icon>
        <svg
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="w-8 h-8"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z"
          />
        </svg>
      </template>
      <template #title>
        Create an account
      </template>
      <div class="px-4">
        <register-form
          :is-quick="true"
          @open-login="openLogin"
          @after-quick-login="afterQuickLogin"
        />
      </div>
    </modal>
  </div>
</template>

<script>
import LoginForm from "./LoginForm.vue"
import RegisterForm from "./RegisterForm.vue"

export default {
  name: "QuickRegister",
  components: {
    LoginForm,
    RegisterForm,
  },
  props: {
    showRegisterModal: {
      type: Boolean,
      required: true,
    },
  },
  emits: ['afterLogin', 'close', 'reopen'],

  setup() {
    return {
      runtimeConfig: useRuntimeConfig(),
    }
  },

  data: () => ({
    showLoginModal: false,
  }),

  mounted() {},

  methods: {
    openLogin() {
      this.showLoginModal = true
      this.$emit("close")
    },
    openRegister() {
      this.showLoginModal = false
      this.$emit("reopen")
    },
    afterQuickLogin() {
      this.showLoginModal = false
      this.$emit("afterLogin")
    },
  },
}
</script>
