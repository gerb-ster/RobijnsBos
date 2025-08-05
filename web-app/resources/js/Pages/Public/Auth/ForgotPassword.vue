<template>
  <Head :title="$t('auth.forgotPassword.title')"/>
  <v-container fluid>
    <v-form @submit.prevent="submit">
      <v-card
        class="mx-auto mt-15 pa-5 bg-surface-light"
        elevation="0"
        max-width="448"
        :title="$t('auth.forgotPassword.title')"
        :subtitle="$t('auth.forgotPassword.subTitle')"
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
            :placeholder=" $t('auth.login.email')"
            prepend-inner-icon="mdi-email-outline"
            variant="outlined"
          ></v-text-field>
          <v-btn
            :disabled="!(form.email)"
            class="mt-5 mb-8"
            elevation="0"
            color="primary"
            size="large"
            block
            type="submit"
          >
            {{ $t('auth.forgotPassword.submitBtn') }}
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
  status: Object
});

console.log(props.status);

const form = useForm({
  email: null
});

const rules = {
  required: [
    value => required(value) || t('form.validation.required')
  ]
}

async function submit(event) {
  const results = await event;

  if (results.valid) {
    form.post(route('forgotPassword.store'));
  }
}

const visible = ref(false);

</script>
