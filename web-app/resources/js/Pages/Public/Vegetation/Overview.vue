<template>
  <Head><title>{{ $t('appName', {page: $t('public.vegetation.overview.title')}) }}</title></Head>
  <v-container fluid>
    <v-row>
      <v-col cols="12" md="3" class="mb-4">
        <v-text-field
          v-model="searchField"
          prepend-inner-icon="mdi-magnify"
          :label="$t('form.search')"
          single-line
          hide-details
          density="comfortable"
          clearable
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="5">
        <v-row>
          <v-col cols="12" md="4">
            <v-select
              v-model="selectedGroup"
              :label="$t('vegetation.fields.area')"
              :items="groups"
              :item-props="groupProps"
              item-value="id"
              density="comfortable"
              clearable
            ></v-select>
          </v-col>
        </v-row>
      </v-col>
    </v-row>
    <v-data-table-server
      v-model:items-per-page="itemsPerPage"
      :headers="headers"
      :items-length="totalItems"
      :items="serverItems"
      :loading="loading"
      :search="search"
      :loading-text="$t('form.loadingText')"
      class="elevation-1"
      item-value="name"
      @update:options="loadItems"
      @click:row="rowClick"
    >
      <template v-slot:loading>
        <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
      </template>
      <template v-slot:item.location="{ item }">
        {{ item.location.x }}, {{ item.location.y }}<span v-if="item.location.xa">, {{ item.location.xa }}, {{ item.location.ya }}</span>
      </template>
      <template v-slot:item.group.name="{ item }">
        {{ item.group.area.name }}<br /><span class="text-medium-emphasis text-caption">{{ item.group.name }}</span>
      </template>
      <template v-slot:item.species.blossom_month="{ item }">
        <span v-for="(month, index) in item.species.blossom_month">
          {{ $t('months.'+month) }}<span v-if="index+1 < item.species.blossom_month.length">, </span>
        </span>
      </template>
    </v-data-table-server>
  </v-container>
</template>

<script setup>

import {router, Head, Link} from '@inertiajs/vue3';
import Confirm from "../../../Components/Confirm.vue";
import {useI18n} from "vue-i18n";
import {ref, watch, onUpdated, onBeforeMount} from 'vue';
import axios from 'axios';
import FlashMessages from "../../../Shared/FlashMessages.vue";
import {openStorage, storeInput} from "../../../Logic/Helpers";

const {t} = useI18n({});

const props = defineProps({
  status: Array,
  groups: Array
});

const headers = ref([
  {title: t('vegetation.fields.label'), align: 'start', key: 'label'},
  {title: t('vegetation.fields.location.name'), align: 'start', key: 'location'},
  {title: t('vegetation.fields.area'), align: 'start', key: 'group.name'},
  {title: t('species.fields.dutchName'), align: 'start', key: 'species.dutch_name'},
  {title: t('species.fields.latinName'), align: 'start', key: 'species.latin_name'},
  {title: t('vegetation.fields.placed'), align: 'start', key: 'placed'},
  {title: t('species.fields.blossomMonth'), align: 'start', key: 'species.blossom_month'}
]);

const search = ref('');
const searchField = ref('');
const searchFieldTimer = ref(null);
const serverItems = ref([]);
const loading = ref(false);
const totalItems = ref(0);

const selectedGroup = ref(null);

const currentPage = ref(1);
const itemsPerPage = ref(25);
const sortBy = ref([]);

onBeforeMount(() => {
  const storedForm = openStorage('vegetationListPublic');

  if (storedForm) {
    // filters
    selectedGroup.value = storedForm['selectedGroup'] ?? null

    // paging & sorting
    currentPage.value = storedForm['currentPage'] ?? 1;
    itemsPerPage.value = storedForm['itemsPerPage'] ?? 25;
    sortBy.value = storedForm['sortBy'] ?? [];
  }
});

watch(searchField, () => {
  clearTimeout(searchFieldTimer.value);

  searchFieldTimer.value = setTimeout(() => {
    search.value = String(Math.random());
  }, 300);
});

watch(sortBy, () => {
  storeInput('vegetationListPublic', 'sortBy', sortBy.value);
});

watch(currentPage, () => {
  storeInput('vegetationListPublic', 'currentPage', currentPage.value);
});

watch(itemsPerPage, () => {
  storeInput('vegetationListPublic', 'itemsPerPage', itemsPerPage.value);
});

watch(selectedGroup, () => {
  storeInput('vegetationListPublic', 'selectedGroup', selectedGroup.value);
  search.value = String(Math.random());
});

onUpdated(() => {
  search.value = String(Math.random());
});

function loadItems({page, itemsPerPage, sortBy}) {
  if (loading.value) {
    return;
  }

  loading.value = true;

  let search = searchField.value;
  let selectedGroupValue = selectedGroup.value;

  axios.post(route('public.vegetation.list'), {
    page,
    itemsPerPage,
    sortBy,
    search,
    selectedGroupValue
  }).then(response => {
    currentPage.value = page;
    serverItems.value = response.data.items;
    totalItems.value = response.data.total;
    loading.value = false;
  }, (error) => {
    console.log(error);
  });
}

function rowClick(event, dataObj) {
  router.get(route('public.vegetation.show', dataObj.item.uuid));
}

function groupProps (item) {
  return {
    title: item.name ?? "removed",
    subtitle: item.area?.name ?? "removed"
  }
}

</script>
