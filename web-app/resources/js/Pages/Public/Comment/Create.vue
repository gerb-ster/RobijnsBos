<template>
  <Head :title="$t('public.comments.createTitle')"/>
  <v-container fluid>
    <flash-messages/>
    <v-form @submit.prevent="submit">
      <v-row>
        <v-col cols="12" md="4">
          <div :class="['text-h5', 'pa-2']">{{ $t('public.comments.createTitle') }}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="form.name"
            :label="$t('public.comments.fields.name')"
            :rules="rules.required"
          ></v-text-field>
          <v-textarea
            v-model="form.remarks"
            :label="$t('public.comments.fields.remark')"
          ></v-textarea>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="4">
          <v-btn
            size="large"
            color="primary"
            prepend-icon="mdi-content-save"
            type="submit"
            elevation="0"
          > {{ $t('form.saveBtn') }}
          </v-btn>
          <Link :href="$route('public.vegetation.show', {vegetation: vegetation.uuid})">
            <v-btn
              prepend-icon="mdi-keyboard-return"
              size="large"
              class="ml-5"
              :href="$route('public.vegetation.show', {vegetation: vegetation.uuid})"
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

import {useForm, Head, Link} from '@inertiajs/vue3';
import {required, email} from '@vee-validate/rules';
import {useI18n} from "vue-i18n";
import FlashMessages from "../../../Shared/FlashMessages.vue";

const props = defineProps(['vegetation']);

const {t} = useI18n({});

const form = useForm({
  name: null,
  remarks: null
});

const rules = {
  required: [
    value => required(value) || t('form.validation.required')
  ]
}

async function submit(event) {
  const results = await event;

  if (results.valid) {
    form.post(route('public.vegetation.comment.store', {
      vegetation: props.vegetation.uuid
    }));
  }
}

</script>
