<template>
  <div class="main-layout min-h-screen flex flex-col">
    <Navbar v-if="useRuntimeConfig().public.displayHeader" />

    <slot class="flex-grow" />
  </div>
</template>
