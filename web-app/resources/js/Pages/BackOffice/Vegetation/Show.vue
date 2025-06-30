<template>
  <Head :title="$t('vegetation.showTitle')"/>
  <v-container fluid>
    <flash-messages/>
    <v-form @submit.prevent="submit">
      <v-row>
        <v-col cols="12" md="4">
          <div :class="['text-h5', 'pa-2']">{{ $t('vegetation.showTitle') }}</div>
        </v-col>
      </v-row>
      <v-card rounded="0">
        <v-tabs v-model="tab">
          <v-tab value="data">{{ $t('vegetation.tabs.data') }}</v-tab>
          <v-tab value="mutations">{{ $t('vegetation.tabs.mutations') }}</v-tab>
          <v-tab value="comments">{{ $t('vegetation.tabs.comments') }}</v-tab>
        </v-tabs>
        <v-card-text>
          <v-window v-model="tab">
            <v-window-item value="data">
              <v-row>
                <v-col cols="12" md="4">
                  <v-card
                    variant="tonal"
                    class="mb-4"
                    color="indigo-darken-3"
                  >
                    <v-card-title>{{ $t('vegetation.fields.location.name') }}</v-card-title>
                    <v-card-text>
                      <v-row>
                        <v-col cols="12" md="6">
                          <v-text-field
                            v-model="form.location.x"
                            :label="$t('vegetation.fields.location.x')"
                            :rules="[rules.required, rules.float]"
                          ></v-text-field>
                        </v-col>
                        <v-col cols="12" md="6">
                          <v-text-field
                            v-model="form.location.y"
                            :label="$t('vegetation.fields.location.y')"
                            :rules="[rules.required, rules.float]"
                          ></v-text-field>
                        </v-col>
                      </v-row>
                    </v-card-text>
                  </v-card>
                  <v-text-field
                    v-model="form.label"
                    :label="$t('vegetation.fields.label')"
                    :rules="[rules.required]"
                  ></v-text-field>
                  <v-select
                    v-model="form.group_id"
                    :label="$t('vegetation.fields.area')"
                    :items="groups"
                    :rules="[rules.required]"
                    :item-props="areaItemProps"
                    item-value="id"
                  ></v-select>
                  <v-select
                    v-model="form.specie_id"
                    :label="$t('vegetation.fields.species')"
                    :items="species"
                    :rules="[rules.required]"
                    :item-props="speciesItemProps"
                    item-value="id"
                  ></v-select>
                  <v-text-field
                    v-model="form.placed"
                    :label="$t('vegetation.fields.placed')"
                    :rules="[rules.required]"
                  ></v-text-field>
                </v-col>
                <v-col cols="12" md="4">
                  <v-text-field
                    v-model="form.amount"
                    :label="$t('vegetation.fields.amount')"
                    :rules="[rules.required]"
                    type="number"
                  ></v-text-field>
                  <v-textarea
                    v-model="form.remarks"
                    :label="$t('vegetation.fields.remarks')"
                  ></v-textarea>
                </v-col>
                <v-col cols="12" md="4">
                  <v-img
                    class="border border-error mb-3"
                    :src="$route('vegetation.showBoard', {vegetation: vegetation.uuid})"
                  ></v-img>
                  <v-btn
                    color="primary"
                    rounded="xl"
                    size="large"
                    block
                    elevation="0"
                    class="mb-3"
                    :href="$route('vegetation.downloadBoard', {vegetation: vegetation.uuid})"
                    target="_blank"
                    prepend-icon="mdi-download-circle-outline"
                  >{{ $t('vegetation.downloadBoardBtn') }}</v-btn>
                  <v-btn
                    color="primary"
                    rounded="xl"
                    size="large"
                    block
                    elevation="0"
                    variant="outlined"
                    :href="$route('public.vegetation.redirect', {shortCode: vegetation.qr_shortcode})"
                    target="_blank"
                    prepend-icon="mdi-link-variant"
                  >{{ $t('vegetation.openPublicProfileBtn') }}</v-btn>
                </v-col>
              </v-row>
            </v-window-item>
            <v-window-item value="mutations">
              <mutations-list :vegetation="vegetation" />
            </v-window-item>
            <v-window-item value="comments">
              <comments-list :vegetation="vegetation"  />
            </v-window-item>
          </v-window>
        </v-card-text>
      </v-card>
      <v-row>
        <v-col cols="12" md="4" class="mt-5">
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
import {ref, computed} from "vue";
import MutationsList from "../../../Components/BackOffice/Vegetation/MutationsList.vue";
import CommentsList from "../../../Components/BackOffice/Vegetation/CommentsList.vue";

const props = defineProps(['vegetation', 'groups', 'species']);

const {t} = useI18n({});

const tab = ref(null);
const form = useForm(props.vegetation);

const rules = {
  required: value => !!value || t('form.validation.required'),
  float: value => {
    const pattern = /^[-+]?[0-9]+(?:\.[0-9]+)?(?:[eE][-+][0-9]+)?$/;
    return pattern.test(value) || t('form.validation.onlyFloats')
  },
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
    form.put(route('vegetation.update', {vegetation: props.vegetation.uuid}));
  }
}

</script>
