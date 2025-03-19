<template>
  <Head :title="$t('groups.showTitle')"/>
  <v-container fluid>
    <flash-messages/>
    <v-form @submit.prevent="submit">
      <v-row>
        <v-col cols="12" md="4">
          <div :class="['text-h5', 'pa-2']">{{ $t('groups.showTitle') }}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="form.name"
            name="name"
            :label="$t('vegetation.fields.name')"
            :rules="rules.required"
          ></v-text-field>
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
          <Link :href="$route('vegetation.index')">
            <v-btn
              prepend-icon="mdi-keyboard-return"
              size="large"
              class="ml-5"
              :href="$route('vegetation.index')"
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

const props = defineProps(['area']);

const {t} = useI18n({});

const form = useForm(props.area);

const rules = {
  required: [
    value => required(value) || t('form.validation.required')
  ]
}

async function submit(event) {
  const results = await event;

  if (results.valid) {
    form.put(route('vegetation.update', props.group.id));
  }
}

</script>
