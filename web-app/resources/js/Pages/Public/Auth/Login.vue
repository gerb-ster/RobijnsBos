<template>
  <v-container fluid>
    <v-form @submit.prevent="submit">
      <v-card
        class="mx-auto pa-12 pb-8 mt-15 bg-surface-light"
        elevation="0"
        max-width="448"
      >
        <flash-messages/>
        <div class="text-subtitle-1 text-medium-emphasis">
          Account
        </div>
        <v-text-field
          v-model="form.email"
          :rules="rules.required"
          density="compact"
          placeholder="Email address"
          prepend-inner-icon="mdi-email-outline"
          variant="outlined"
        ></v-text-field>
        <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
          Password
        </div>
        <v-text-field
          v-model="form.password"
          :rules="rules.required"
          :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
          :type="visible ? 'text' : 'password'"
          density="compact"
          placeholder="Enter your password"
          prepend-inner-icon="mdi-lock-outline"
          variant="outlined"
          @click:append-inner="visible = !visible"
        ></v-text-field>
        <v-btn
          class="mt-5 mb-8"
          color="light-green-darken-4"
          size="large"
          variant="tonal"
          block
          type="submit"
        >
          Log In
        </v-btn>
      </v-card>
    </v-form>
  </v-container>
</template>

<script setup>

import {Head, useForm} from "@inertiajs/vue3";
import {ref} from "vue";
import {required} from "@vee-validate/rules";
import {useI18n} from "vue-i18n";
import FlashMessages from "../../../Shared/FlashMessages.vue";

const {t} = useI18n({});

const form = useForm({
  email: null,
  password: null
});

const rules = {
  required: [
    value => required(value) || t('form.validation.required')
  ]
}

async function submit(event) {
  const results = await event;

  if (results.valid) {
    form.post(route('login.store'));
  }
}

const visible = ref(false);

</script>
