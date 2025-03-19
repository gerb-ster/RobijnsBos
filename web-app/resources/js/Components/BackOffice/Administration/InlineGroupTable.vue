<template>
  <Confirm ref="confirmDelete"></Confirm>
  <v-data-table
    :headers="headers"
    :items="groups"
    item-key="id"
    :class="$vuetify.theme.global.current.dark?('expandedRowDark'):('expandedRow')"
    density="compact"
    :items-per-page="0"
  >
    <template #bottom></template>
    <template v-slot:item.actions="{ item }">
      <div class="text-end" @click.stop>
        <v-icon
          @click="editItem(item)"
          size="small"
          class="mr-2"
        >
          mdi-pencil-outline
        </v-icon>
        <v-icon
          size="small"
          @click.stop="deleteItem(item)"
        >
          mdi-trash-can-outline
        </v-icon>
      </div>
    </template>
  </v-data-table>
  <v-container fluid>
    <v-row>
      <v-col cols="12" md="4">
      </v-col>
      <v-col cols="12" md="8">
        <div class="text-end">
          <Link :href="$route('groups.create', {area: area.id})">
            <v-btn
              variant="tonal"
              prepend-icon="mdi-plus"
              color="plain"
              :href="$route('groups.create', {area: area.id})"
            >
              {{ t('groups.createBtn') }}
            </v-btn>
          </Link>
        </div>
      </v-col>
    </v-row>
  </v-container>
</template>
<script setup>

import {useI18n} from "vue-i18n";
import {Link, router} from "@inertiajs/vue3";
import Confirm from "../../Confirm.vue";
import {ref} from "vue";

const { t } = useI18n({});

const props = defineProps(['area', 'groups']);

const search = ref("");
const confirmDelete = ref(null);

const headers = [
  { title: t('groups.fields.name'), value: 'name' },
  { title: t('form.actions'), align: 'end', key: 'actions', sortable: false },
];

function editItem (item) {
  router.get(route('groups.show', {
    area: props.area,
    group: item.id
  }));
}

function deleteItem (item) {
  confirmDelete.value.open(
    t('form.confirmRemoveDialogTitle'),
    t('groups.confirmRemoveDialogText', {name: item.name}),{
      color: 'secondary'
    }
  ).then((confirm) => {
    if (confirm) {
      router.delete(route('groups.destroy', {
        area: props.area,
        group: item.id
      }));
    }
  });
}

</script>
