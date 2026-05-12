<template>
  <Head :title="$t('auth.login.title')"/>
  <v-container fluid>
    <v-form @submit.prevent="submit">
      <v-card
        class="mx-auto mt-15 pa-5 bg-surface-light"
        elevation="0"
        max-width="448"
        :title="$t('auth.login.title')"
        :subtitle="$t('auth.login.subTitle')"
      >
        <v-card-text>
          <flash-messages/>
          <div class="text-subtitle-1 text-medium-emphasis">
            {{ $t('auth.login.email') }}
          </div>
          <v-text-field
            v-model="form.email"
            :rules="rules.required"
            density="compact"
            :placeholder="$t('auth.login.email')"
            prepend-inner-icon="mdi-email-outline"
            variant="outlined"
          ></v-text-field>
          <div class="text-subtitle-1 text-medium-emphasis d-flex align-center justify-space-between">
            {{ $t('auth.login.password') }}
          </div>
          <v-text-field
            v-model="form.password"
            :rules="rules.required"
            :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
            :type="visible ? 'text' : 'password'"
            density="compact"
            prepend-inner-icon="mdi-lock-outline"
            variant="outlined"
            @click:append-inner="visible = !visible"
            :placeholder=" $t('auth.login.password')"
          ></v-text-field>
          <v-btn
            :disabled="!(form.email && form.password)"
            class="mt-5 mb-8"
            elevation="0"
            color="primary"
            size="large"
            block
            type="submit"
          >
            {{ $t('auth.login.signInBtn') }}
          </v-btn>
          <a :href="route('forgotPassword')">{{ $t('auth.login.forgotPasswordLink') }}</a>
        </v-card-text>
      </v-card>
    </v-form>
  </v-container>
</template>

<script setup>

import {Head, useForm} from "@inertiajs/vue3";
import {ref, inject} from "vue";
import {required} from "@vee-validate/rules";
import {useI18n} from "vue-i18n";
import FlashMessages from "../../../Shared/FlashMessages.vue";
const route = inject('route');

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
