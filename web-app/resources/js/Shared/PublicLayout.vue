<template>
  <v-app id="layout">
    <confirm ref="confirmSignOut"></confirm>
    <v-app-bar elevation="0" flat style="border-bottom: 1px solid #d2d2d2 !important;">
      <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      <v-app-bar-title>
        <v-img
          src='/images/logo_full.png'
          max-height="40"
          max-width="200"
        ></v-img>
      </v-app-bar-title>
      <v-spacer></v-spacer>
      <language-select :auth="auth"></language-select>
      <v-list v-if="auth.user !== null">
        <v-list-item
          :title="auth.user.name"
        ></v-list-item>
      </v-list>
      <span class="me-7"></span>
    </v-app-bar>
    <v-navigation-drawer
      v-model="drawer"
      location="left"
      temporary
    >
      <v-list>
        <Link as="div" :href="$route('homePage.index')">
          <v-list-item
            prepend-icon="mdi-home"
            :title="$t('public.navigation.home')"
            value="home"
            :href="$route('homePage.index')"
            @click="drawer=!drawer"
          ></v-list-item>
        </Link>
        <v-divider></v-divider>
        <Link as="div" :href="$route('public.vegetation.overview')">
          <v-list-item
            prepend-icon="mdi-forest-outline"
            :title="$t('public.navigation.overview')"
            value="overview"
            :href="$route('public.vegetation.overview')"
            @click="drawer=!drawer"
          ></v-list-item>
        </Link>
        <Link as="div" :href="$route('public.vegetation.map')">
          <v-list-item
            prepend-icon="mdi-map-legend"
            :title="$t('public.navigation.map')"
            value="map"
            :href="$route('public.vegetation.map')"
            @click="drawer=!drawer"
          ></v-list-item>
        </Link>
        <v-divider></v-divider>
        <Link as="div" :href="$route('login')" v-if="auth.user === null">
          <v-list-item
            prepend-icon="mdi-key-chain-variant"
            :title="$t('public.navigation.login')"
            value="login"
            :href="$route('login')"
            @click="drawer=!drawer"
          ></v-list-item>
        </Link>
        <template v-else>
          <Link as="div" :href="$route('vegetation.index')">
            <v-list-item
              v-if="auth.user.canAccessBackOffice"
              prepend-icon="mdi-office-building-cog-outline"
              :title="$t('public.navigation.backOffice')"
              value="backOffice"
              :href="$route('vegetation.index')"
            ></v-list-item>
          </Link>
          <v-list-item
            prepend-icon="mdi-logout"
            :title="$t('public.navigation.signOut')"
            value="signOut"
            @click="confirmSignOutCall()"
          ></v-list-item>
        </template>
      </v-list>
    </v-navigation-drawer>
    <v-main>
      <slot/>
    </v-main>
  </v-app>
</template>

<script setup>

import {Link, router} from "@inertiajs/vue3";
import {ref} from "vue";
import Confirm from "../Components/Confirm.vue";
import {useI18n} from "vue-i18n";
import LanguageSelect from "../Components/AppBar/LanguageSelect.vue";

const props = defineProps(['auth']);

const drawer = ref(false);
const group = ref(null);

const {t, locale} = useI18n();

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
