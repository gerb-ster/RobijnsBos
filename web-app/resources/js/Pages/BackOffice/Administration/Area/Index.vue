<template>
  <Head><title>{{ $t('areas.listTitle') }}</title></Head>
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
          clearable
        ></v-text-field>
      </v-col>
      <v-col cols="12" md="7">
        <v-checkbox
          v-model="showDeleted"
          :label="$t('areas.withTrashed')"
        ></v-checkbox>
      </v-col>
      <v-col cols="12" md="2">
        <Link as="div" :href="$route('areas.create')">
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
      :row-props="setRowProps"
      show-expand
      expand-on-click
    >
      <template v-slot:loading>
        <v-skeleton-loader type="table-row@10"></v-skeleton-loader>
      </template>
      <template v-slot:item.actions="{ item }">
        <v-icon @click="editItem(item)" class="mr-2">
          mdi-pencil-outline
        </v-icon>
        <v-icon @click.stop="deleteItem(item)" v-if="!item.deleted_at">
          mdi-trash-can-outline
        </v-icon>
        <v-icon @click.stop="restoreItem(item)" v-else>
          mdi-delete-restore
        </v-icon>
      </template>
      <template v-slot:expanded-row="{ columns, item }">
        <tr :class="$vuetify.theme.global.current.dark?('expandedRowDark'):('expandedRow')">
          <td :colspan="columns.length">
            <inline-group-table
              :area="item"
              :groups="item.groups"
            ></inline-group-table>
          </td>
        </tr>
      </template>
    </v-data-table-server>
  </v-container>
</template>

<script setup>

import {router, Head, Link} from '@inertiajs/vue3';
import Confirm from "../../../../Components/Confirm.vue";
import {useI18n} from "vue-i18n";
import {ref, watch, onUpdated, onBeforeMount} from 'vue';
import axios from 'axios';
import FlashMessages from "../../../../Shared/FlashMessages.vue";
import {openStorage, storeInput} from "../../../../Logic/Helpers";
import InlineGroupTable from "../../../../Components/BackOffice/Administration/InlineGroupTable.vue";

const {t} = useI18n({});

const headers = ref([
  {align: 'start', key: 'data-table-expand'},
  {title: t('areas.fields.name'), align: 'start', key: 'name'},
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
  const storedForm = openStorage('areaAdmin');

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
    storeInput('userAdmin', 'searchField', searchField.value);
    search.value = String(Math.random());
  }, 300);
});

watch(showDeleted, () => {
  storeInput('userAdmin', 'searchField', showDeleted.value);
  search.value = String(Math.random());
});

watch(sortBy, () => {
  storeInput('userAdmin', 'sortBy', sortBy.value);
});

watch(currentPage, () => {
  storeInput('userAdmin', 'currentPage', currentPage.value);
});

watch(itemsPerPage, () => {
  storeInput('userAdmin', 'itemsPerPage', itemsPerPage.value);
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

  axios.post(route('areas.list'), {
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

function editItem(item) {
  if (!item.deleted_at) {
    router.get(route('areas.show', item.id));
  }
}

function deleteItem(item) {
  confirmDelete.value.open(
    t('form.confirmRemoveDialogTitle'),
    t('areas.confirmRemoveDialogText', {name: item.name}), {
      color: 'secondary'
    }
  ).then((confirm) => {
    if (confirm) {
      router.delete(route('areas.destroy', {area: item}));
    }
  });
}

function restoreItem(item) {
  confirmRestore.value.open(
    t('form.confirmRestoreDialogTitle'),
    t('areas.confirmRestoreDialogText', {name: item.name}), {
      color: 'primary'
    }
  ).then((confirm) => {
    if (confirm) {
      router.get(route('areas.restore', {id: item.id}));
    }
  });
}

function setRowProps(row) {
  if (row.item.deleted_at) {
    return {class: 'removedRow'};
  }
}

</script>
