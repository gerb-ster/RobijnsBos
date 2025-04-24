<template>
  <Head :title="$t('users.createTitle')"/>
  <v-container fluid>
    <flash-messages/>
    <v-form @submit.prevent="submit">
      <v-row>
        <v-col cols="12" md="4">
          <div :class="['text-h5', 'pa-2']">{{ $t('users.createTitle') }}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="form.name"
            :label="$t('users.fields.name')"
            :rules="rules.required"
            required
          ></v-text-field>
          <v-text-field
            v-model="form.email"
            :label="$t('users.fields.email')"
            :rules="rules.required.concat(rules.email)"
            required
          ></v-text-field>
          <v-text-field
            v-model="form.password"
            :label="$t('users.fields.password')"
            :rules="rules.required"
            type="password"
            required
          ></v-text-field>
          <v-select
            v-model="form.role_id"
            :label="$t('users.fields.role')"
            :items="roles"
            :item-title="item => $t('roles.'+item.name)"
            :rules="rules.required"
            item-value="id"
          ></v-select>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="4">
          <v-btn
            prepend-icon="mdi-content-save"
            size="large"
            color="primary"
            type="submit"
            elevation="0"
          > {{ $t('form.saveBtn') }}
          </v-btn>
          <Link :href="$route('users.index')">
            <v-btn
              prepend-icon="mdi-keyboard-return"
              size="large"
              class="ml-5"
              :href="$route('users.index')"
              elevation="0"
            > {{ $t('form.backBtn') }}
            </v-btn>
          </Link>
        </v-col>
      </v-row>
    </v-form>
  </v-container>
</template>

<script setup>

import {Head, Link, useForm} from '@inertiajs/vue3';
import {email, required} from "@vee-validate/rules";
import {useI18n} from "vue-i18n";
import FlashMessages from "../../../../Shared/FlashMessages.vue";

const props = defineProps(['roles']);

const {t} = useI18n({});

const form = useForm({
  name: null,
  email: null,
  password: null,
  role_id: null
});

const rules = {
  required: [
    value => required(value) || t('form.validation.required')
  ],
  email: [
    value => email(value) || t('form.validation.email')
  ],
}

async function submit(event) {
  const results = await event;

  if (results.valid) {
    form.post(route('users.store'));
  }
}

</script>
