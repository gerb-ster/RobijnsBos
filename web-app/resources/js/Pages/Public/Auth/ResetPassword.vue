<template>
  <Head :title="$t('auth.resetPassword.title')"/>
  <v-container fluid>
    <v-form @submit.prevent="submit">
      <v-card
        class="mx-auto mt-15 pa-5 bg-surface-light"
        elevation="0"
        max-width="448"
        :title="$t('auth.resetPassword.title')"
        :subtitle="$t('auth.resetPassword.subTitle')"
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
            :placeholder=" $t('auth.resetPassword.email')"
            prepend-inner-icon="mdi-email-outline"
            variant="outlined"
          ></v-text-field>
          <v-text-field
            v-model="form.password"
            :rules="rules.required"
            density="compact"
            :placeholder=" $t('auth.resetPassword.password')"
            prepend-inner-icon="mdi-lock-outline"
            variant="outlined"
            :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
            :type="visible ? 'text' : 'password'"
            @click:append-inner="visible = !visible"
          ></v-text-field>
          <v-text-field
            v-model="form.password_confirmation"
            :rules="rules.required"
            density="compact"
            :placeholder=" $t('auth.resetPassword.passwordConfirmation')"
            prepend-inner-icon="mdi-lock-outline"
            variant="outlined"
            :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
            :type="visible ? 'text' : 'password'"
            @click:append-inner="visible = !visible"
          ></v-text-field>
          <v-btn
            :disabled="!(form.password)"
            class="mt-5 mb-8"
            elevation="0"
            color="primary"
            size="large"
            block
            type="submit"
          >
            {{ $t('auth.resetPassword.submitBtn') }}
          </v-btn>
        </v-card-text>
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

const props = defineProps({
  token: String
});

console.log(props.token);

const form = useForm({
  email: null,
  password: null,
  password_confirmation: null,
  token: props.token
});

const rules = {
  required: [
    value => required(value) || t('form.validation.required')
  ]
}

async function submit(event) {
  const results = await event;

  if (results.valid) {
    form.post(route('password.update'));
  }
}

const visible = ref(false);

</script>
