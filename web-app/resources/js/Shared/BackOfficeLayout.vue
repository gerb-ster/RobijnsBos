<template>
  <Confirm ref="confirmSignOut"></Confirm>
  <v-app id="layout">
    <v-app-bar elevation="0" flat style="border-bottom: 1px solid #d2d2d2 !important;">
      <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      <v-app-bar-title>{{ $t('appBar.appTitle') }}</v-app-bar-title>
      <v-spacer></v-spacer>
      <language-select :auth="auth"></language-select>
      <v-list>
        <v-list-item
          :title="auth.user.name"
        ></v-list-item>
      </v-list>
      <v-tooltip
        :text="$t('appBar.userIsAdministrator')"
        location="bottom"
        v-if="auth.user.admin"
      >
        <template v-slot:activator="{ props }">
          <v-icon
            icon="mdi-shield-crown"
            class="me-7"
            v-bind="props"
          ></v-icon>
        </template>
      </v-tooltip>
    </v-app-bar>
    <v-navigation-drawer
      v-model="drawer"
      location="left"
      temporary
    >
      <v-list>
        <div v-if="auth.user.admin">
          <v-divider></v-divider>
          <Link as="div" :href="$route('user.index')">
            <v-list-item
              prepend-icon="mdi-account-group-outline"
              :title="$t('navigation.userLink')"
              value="users"
              :href="$route('user.index')"
              @click="drawer=!drawer"
            ></v-list-item>
          </Link>
        </div>
        <v-divider></v-divider>
        <v-list-item
          prepend-icon="mdi-logout"
          :title="$t('navigation.signOutLink')"
          value="signout"
          @click="confirmSignOutCall()"
        ></v-list-item>
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <slot/>
    </v-main>
  </v-app>
</template>

<script setup>
import {ref, watch} from 'vue'
import {useI18n} from "vue-i18n";
import {Link, router} from '@inertiajs/vue3';

import LanguageSelect from "../Components/AppBar/LanguageSelect.vue";
import Confirm from "../Components/Confirm.vue";

const props = defineProps(['settings', 'auth']);

const {t, locale} = useI18n();
locale.value = props.auth?.user.locale || 'en';

const drawer = ref(false)
const group = ref(null)

watch(group, () => {
  drawer.value = false
});

const confirmSignOut = ref(null);

function confirmSignOutCall() {
  drawer.value = false;

  confirmSignOut.value.open(
    t('navigation.confirmSignOutTitle'),
    t('navigation.confirmSignOutText'), {
      color: 'primary'
    }
  ).then((confirm) => {
    if (confirm) {
      router.delete(route('logout'));
    }
  });
}

</script>
