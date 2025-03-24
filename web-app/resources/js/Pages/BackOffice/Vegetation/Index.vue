<template>
  <Head><title>{{ $t('vegetation.listTitle') }}</title></Head>
  <confirm ref="confirmDelete"></confirm>
  <confirm ref="confirmRestore"></confirm>
  <v-container fluid>
    <flash-messages/>
    <v-row>
      <v-col cols="12" md="3">
        <v-text-field
          v-model="searchField"
          prepend-inner-icon="mdi-magnify"
          :label="$t('form.search')"
          single-line
          hide-details
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="7">
        <v-checkbox
          v-model="showDeleted"
          :label="$t('vegetation.withTrashed')"
        ></v-checkbox>
      </v-col>
      <v-col cols="12" md="2">
        <Link as="div" :href="$route('vegetation.create')">
          <v-btn
            icon="mdi-plus"
            color="primary"
            class="float-end"
            elevation="0"
          ></v-btn>
        </Link>
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
      :row-props="setRowProps"
    >
      <template v-slot:loading>
        <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
      </template>
      <template v-slot:item.status.name="{ item }">
        {{ $t('vegetationStatus.'+item.status.name) }}
      </template>
      <template v-slot:item.location="{ item }">
        {{ item.location.x }}, {{ item.location.y }}<span v-if="item.location.xa">, {{ item.location.xa }}, {{ item.location.ya }}</span>
      </template>
      <template v-slot:item.group.name="{ item }">
        {{ item.group.area.name }} / {{ item.group.name }}
      </template>
      <template v-slot:item.species.blossom_month="{ item }">
        <span v-for="(month, index) in item.species.blossom_month">
          {{ $t('months.'+month) }}<span v-if="index+1 < item.species.blossom_month.length">, </span>
        </span>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon @click.stop="deleteItem(item)" v-if="!item.deleted_at">
          mdi-trash-can-outline
        </v-icon>
        <v-icon @click.stop="restoreItem(item)" v-else>
          mdi-delete-restore
        </v-icon>
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

const headers = ref([
  {title: t('vegetation.fields.number'), align: 'start', key: 'number'},
  {title: t('vegetation.fields.status'), align: 'start', key: 'status.name'},
  {title: t('vegetation.fields.location.name'), align: 'start', key: 'location'},
  {title: t('vegetation.fields.area'), align: 'start', key: 'group.name'},
  {title: t('species.fields.dutchName'), align: 'start', key: 'species.dutch_name'},
  {title: t('species.fields.latinName'), align: 'start', key: 'species.latin_name'},
  {title: t('vegetation.fields.placed'), align: 'start', key: 'placed'},
  {title: t('species.fields.blossomMonth'), align: 'start', key: 'species.blossom_month'},
  {title: t('form.actions'), align: 'end', key: 'actions', sortable: false},
]);

const search = ref('');
const searchField = ref('');
const searchFieldTimer = ref(null);
const serverItems = ref([]);
const loading = ref(false);
const totalItems = ref(0);
const showDeleted = ref(false);

const confirmDelete = ref(null);
const confirmRestore = ref(null);

const currentPage = ref(1);
const itemsPerPage = ref(25);
const sortBy = ref([]);

onBeforeMount(() => {
  const storedForm = openStorage('vegetationAdmin');

  if (storedForm) {
    // filters
    searchField.value = storedForm['searchField'] ?? '';
    showDeleted.value = storedForm['showDeleted'] ?? false;

    // paging & sorting
    currentPage.value = storedForm['currentPage'] ?? 1;
    itemsPerPage.value = storedForm['itemsPerPage'] ?? 25;
    sortBy.value = storedForm['sortBy'] ?? [];
  }
});

watch(searchField, () => {
  clearTimeout(searchFieldTimer.value);

  searchFieldTimer.value = setTimeout(() => {
    storeInput('vegetationAdmin', 'searchField', searchField.value);
    search.value = String(Math.random());
  }, 300);
});

watch(showDeleted, () => {
  storeInput('vegetationAdmin', 'searchField', showDeleted.value);
  search.value = String(Math.random());
});

watch(sortBy, () => {
  storeInput('vegetationAdmin', 'sortBy', sortBy.value);
});

watch(currentPage, () => {
  storeInput('vegetationAdmin', 'currentPage', currentPage.value);
});

watch(itemsPerPage, () => {
  storeInput('vegetationAdmin', 'itemsPerPage', itemsPerPage.value);
});

onUpdated(() => {
  search.value = String(Math.random());
});

function loadItems({page, itemsPerPage, sortBy}) {
  if (loading.value) {
    return;
  }

  loading.value = true;

  let withTrashed = showDeleted.value;
  let search = searchField.value;

  axios.post(route('vegetation.list'), {
    page,
    itemsPerPage,
    sortBy,
    search,
    withTrashed
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
  if (!dataObj.item.deleted_at) {
    router.get(route('vegetation.show', dataObj.item.uuid));
  }
}

function deleteItem(item) {
  confirmDelete.value.open(
    t('form.confirmRemoveDialogTitle'),
    t('vegetation.confirmRemoveDialogText', {name: item.name}), {
      color: 'secondary'
    }
  ).then((confirm) => {
    if (confirm) {
      router.delete(route('vegetation.destroy', {user: item}));
    }
  });
}

function restoreItem(item) {
  confirmRestore.value.open(
    t('form.confirmRestoreDialogTitle'),
    t('vegetation.confirmRestoreDialogText', {name: item.name}), {
      color: 'primary'
    }
  ).then((confirm) => {
    if (confirm) {
      router.get(route('vegetation.restore', {id: item.id}));
    }
  });
}

function setRowProps(row) {
  if (row.item.deleted_at) {
    return {class: 'removedRow'};
  }
}

</script>
