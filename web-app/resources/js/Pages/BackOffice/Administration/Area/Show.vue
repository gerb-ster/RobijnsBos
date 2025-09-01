<template>
  <Head :title="$t('areas.showTitle')"/>
  <v-container fluid>
    <flash-messages/>
    <v-form @submit.prevent="submit">
      <v-row>
        <v-col cols="12" md="4">
          <div :class="['text-h5', 'pa-2']">{{ $t('areas.showTitle') }}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="form.name"
            name="name"
            :label="$t('areas.fields.name')"
            :rules="rules.required"
          ></v-text-field>
          <v-card
            variant="tonal"
            class="mb-4"
            color="indigo-darken-3"
          >
            <v-card-title>{{ $t('areas.fields.coordinates.name') }}</v-card-title>
            <v-card-text>
              <v-row class="mb-0">
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="form.coordinates.xTop"
                    :label="$t('areas.fields.coordinates.xTop')"
                    :rules="[rules.required, rules.float]"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="form.coordinates.yTop"
                    :label="$t('areas.fields.coordinates.yTop')"
                    :rules="[rules.required, rules.float]"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="form.coordinates.xBottom"
                    :label="$t('areas.fields.coordinates.xBottom')"
                    :rules="[rules.required, rules.float]"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="3">
                  <v-text-field
                    v-model="form.coordinates.yBottom"
                    :label="$t('areas.fields.coordinates.yBottom')"
                    :rules="[rules.required, rules.float]"
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
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
          <Link :href="$route('areas.index')">
            <v-btn
              prepend-icon="mdi-keyboard-return"
              size="large"
              class="ml-5"
              :href="$route('areas.index')"
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
import FlashMessages from "../../../../Shared/FlashMessages.vue";

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
    form.put(route('areas.update', props.area.id));
  }
}

</script>
