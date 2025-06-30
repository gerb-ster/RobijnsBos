<template>
  <Head :title="$t('vegetation.createTitle')"/>
  <v-container fluid>
    <flash-messages/>
    <v-form @submit.prevent="submit">
      <v-row>
        <v-col cols="12" md="4">
          <div :class="['text-h5', 'pa-2']">{{ $t('vegetation.createTitle') }}</div>
        </v-col>
      </v-row>
      <v-row>
        <v-col cols="12" md="4">
          <v-card
            variant="tonal"
            class="mb-4"
            color="indigo-darken-3"
          >
            <v-card-title>{{ $t('vegetation.fields.location.name') }}</v-card-title>
            <v-card-text>
              <v-row class="mb-0">
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.location.x"
                    :label="$t('vegetation.fields.location.x')"
                    :rules="rules.required"
                    required
                    hide-details
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="6">
                  <v-text-field
                    v-model="form.location.y"
                    :label="$t('vegetation.fields.location.y')"
                    :rules="rules.required"
                    required
                    hide-details
                  ></v-text-field>
                </v-col>
              </v-row>
            </v-card-text>
          </v-card>
          <v-text-field
            v-model="form.label"
            :label="$t('vegetation.fields.label')"
            :rules="rules.required"
          ></v-text-field>
          <v-select
            v-model="form.group_id"
            :label="$t('vegetation.fields.area')"
            :items="groups"
            :rules="rules.required"
            :item-props="areaItemProps"
            item-value="id"
          ></v-select>
          <v-select
            v-model="form.specie_id"
            :label="$t('vegetation.fields.species')"
            :items="species"
            :rules="rules.required"
            :item-props="speciesItemProps"
            item-value="id"
          ></v-select>
          <v-text-field
            v-model="form.placed"
            :label="$t('vegetation.fields.placed')"
            :rules="rules.required"
            required
          ></v-text-field>
        </v-col>
        <v-col cols="12" md="4">
          <v-text-field
            v-model="form.amount"
            :label="$t('vegetation.fields.amount')"
            :rules="rules.required"
            required
            type="number"
          ></v-text-field>
          <v-textarea
            v-model="form.remarks"
            :label="$t('vegetation.fields.remarks')"
          ></v-textarea>
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

import {Head, Link, useForm} from '@inertiajs/vue3';
import {email, required} from "@vee-validate/rules";
import {useI18n} from "vue-i18n";
import FlashMessages from "../../../Shared/FlashMessages.vue";

const props = defineProps(['groups', 'species']);

const {t} = useI18n({});

const form = useForm({
  location: {x : null, y: null},
  label: null,
  group_id: null,
  specie_id: null,
  placed: null,
  amount: 1,
  remarks: null
});

const rules = {
  required: [
    value => required(value) || t('form.validation.required')
  ]
}

function areaItemProps (item) {
  return {
    title: item.name,
    subtitle: item.area.name,
  }
}

function speciesItemProps (item) {
  return {
    title: item.dutch_name,
    subtitle: item.latin_name,
  }
}

async function submit(event) {
  const results = await event;

  if (results.valid) {
    form.post(route('vegetation.store'));
  }
}

</script>
