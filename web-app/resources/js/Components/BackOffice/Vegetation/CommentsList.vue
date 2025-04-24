<template>
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
        :label="$t('comments.withTrashed')"
      ></v-checkbox>
    </v-col>
  </v-row>
  <v-data-table
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
    density="comfortable"
  >
    <template v-slot:item.status.name="{ item }">
      <strong :class="getStatusColor(item.status.name)">{{ $t('commentStatus.'+item.status.name) }}</strong>
    </template>
    <template v-slot:item.actions="{ item }">
      <div class="text-end" @click.stop>
        <v-icon
          size="small"
          @click.stop="deleteItem(item)"
        >
          mdi-trash-can-outline
        </v-icon>
      </div>
    </template>
  </v-data-table>
</template>

<script setup>

import {useI18n} from "vue-i18n";
import {Link, router} from "@inertiajs/vue3";
import {ref} from "vue";
import axios from "axios";

const { t } = useI18n({});

const props = defineProps(['vegetation']);

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
const itemsPerPage = ref(10);
const sortBy = ref([]);

const headers = [
  { title: t('comments.fields.number'), value: 'number' },
  { title: t('comments.fields.status'), value: 'status.name' },
  { title: t('comments.fields.name'), value: 'name' },
  { title: t('comments.fields.remarks'), value: 'remarks' },
  { title: t('form.actions'), align: 'end', key: 'actions', sortable: false },
];

function loadItems({page, itemsPerPage, sortBy}) {
  if (loading.value) {
    return;
  }

  loading.value = true;
  loading.value = true;

  let withTrashed = showDeleted.value;
  let search = searchField.value;

  axios.post(route('comments.list', {vegetation: props.vegetation.uuid}), {
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
  router.get(route('comments.show', {
    vegetation: props.vegetation.uuid,
    comment: dataObj.item.uuid
  }));
}

function deleteItem (item) {
  confirmDelete.value.open(
    t('form.confirmRemoveDialogTitle'),
    t('comments.confirmRemoveDialogText', {name: item.name}),{
      color: 'secondary'
    }
  ).then((confirm) => {
    if (confirm) {
      router.delete(route('comments.destroy', {
        vegetation: props.vegetation.uuid,
        comment: item.uuid
      }));
    }
  });
}

/**
 * getStatusColor
 * @param status
 * @returns {string}
 */
function getStatusColor(status) {
  switch (status) {
    case "approved":
      return "text-green";
    case "declined":
      return "text-red";
  }
}

</script>
