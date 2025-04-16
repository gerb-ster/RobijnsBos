<template>
  <v-app id="layout">
    <v-main class="publicGradient">
      <v-container>
        <v-row>
          <v-col>
            <v-sheet
              max-width="96%"
              class="mx-auto"
              color="transparent"
            >
              <v-row>
                <v-col>
                  <v-img
                    src='/images/logo_full.png'
                    max-height="100"
                    max-width="380"
                  ></v-img>
                </v-col>
              </v-row>
            </v-sheet>
          </v-col>
        </v-row>
        <v-row>
          <v-col>
            <v-sheet
              class="mx-auto"
              rounded="0"
              elevation="0"
              max-width="96%"
              min-height="480"
            >
              <slot/>
            </v-sheet>
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>

import {useI18n} from "vue-i18n";

const props = defineProps(['settings', 'auth']);

const {t, locale} = useI18n();
locale.value = 'nl';

</script>
