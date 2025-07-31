<template>
  <v-app id="layout">
    <confirm ref="confirmSignOut"></confirm>
    <v-app-bar elevation="0" flat style="border-bottom: 1px solid #d2d2d2 !important;">
      <v-app-bar-nav-icon variant="text" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
      <v-img
        src='/images/logo.png'
        max-height="40"
        max-width="100"
      ></v-img>
      <v-app-bar-title>
        <span class="logoText">{{ $t('appTitleBar') }}</span>
        <span v-if="selected" class="appTitleText"> - {{ $t('navigation.' + selected) }}</span>
      </v-app-bar-title>
      <v-spacer></v-spacer>
      <!-- <language-select :auth="auth"></language-select> -->
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
      <v-list @update:selected="select">
        <Link as="div" :href="$route('public.vegetation.map')">
          <v-list-item
            prepend-icon="mdi-map-legend"
            :title="$t('navigation.map')"
            value="map"
            :href="$route('public.vegetation.map')"
            @click="drawer=!drawer"
          ></v-list-item>
        </Link>
        <template v-if="auth.user === null">
          <Link as="div" :href="$route('public.vegetation.overview')">
            <v-list-item
              prepend-icon="mdi-forest-outline"
              :title="$t('navigation.overview')"
              value="overview"
              :href="$route('public.vegetation.overview')"
              @click="drawer=!drawer"
            ></v-list-item>
          </Link>
          <v-divider></v-divider>
          <Link as="div" :href="$route('login')">
            <v-list-item
              prepend-icon="mdi-key-chain-variant"
              :title="$t('navigation.login')"
              value="login"
              :href="$route('login')"
              @click="drawer=!drawer"
            ></v-list-item>
          </Link>
        </template>
        <template v-else>
          <template v-if="auth.user.canAccessBackOffice">
            <Link as="div" :href="$route('vegetation.index')">
              <v-list-item
                prepend-icon="mdi-forest-outline"
                :title="$t('navigation.vegetation')"
                value="vegetation"
                :href="$route('vegetation.index')"
                @click="drawer=!drawer"
              ></v-list-item>
            </Link>
            <template v-if="auth.user.canAdministrate">
              <v-divider></v-divider>
              <Link as="div" :href="$route('species.index')">
                <v-list-item
                  prepend-icon="mdi-sprout"
                  :title="$t('navigation.species')"
                  value="species"
                  :href="$route('species.index')"
                  @click="drawer=!drawer"
                ></v-list-item>
              </Link>
              <Link as="div" :href="$route('areas.index')">
                <v-list-item
                  prepend-icon="mdi-map-legend"
                  :title="$t('navigation.areaGroup')"
                  value="areaGroup"
                  :href="$route('areas.index')"
                  @click="drawer=!drawer"
                ></v-list-item>
              </Link>
              <Link as="div" :href="$route('users.index')">
                <v-list-item
                  prepend-icon="mdi-account-group-outline"
                  :title="$t('navigation.users')"
                  value="users"
                  :href="$route('users.index')"
                  @click="drawer=!drawer"
                ></v-list-item>
              </Link>
            </template>
          </template>
          <template v-else>
            <Link as="div" :href="$route('public.vegetation.overview')">
              <v-list-item
                prepend-icon="mdi-forest-outline"
                :title="$t('navigation.overview')"
                value="overview"
                :href="$route('public.vegetation.overview')"
                @click="drawer=!drawer"
              ></v-list-item>
            </Link>
          </template>
          <v-divider></v-divider>
          <v-list-item
            prepend-icon="mdi-logout"
            :title="$t('navigation.signOut')"
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
import {ref, watch} from "vue";
import Confirm from "../Components/Confirm.vue";
import {useI18n} from "vue-i18n";
import LanguageSelect from "../Components/AppBar/LanguageSelect.vue";

const props = defineProps({
  auth: Object
});

const drawer = ref(false);
const selected = ref(['map']);

const {t, locale} = useI18n();

const confirmSignOut = ref(null);

function select(item){
  if(item.length === 1) {
    selected.value = item;
  }
}

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
