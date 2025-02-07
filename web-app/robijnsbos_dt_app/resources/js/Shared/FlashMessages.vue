<template>
  <v-alert
    v-if="$page.props.flash.success && showAlert"
    closable
    type="success"
    :title="$t('flashMessages.successTitle')"
    :text="$t($page.props.flash.success)"
    class="mb-4"
  ></v-alert>
  <v-alert
    :value="showAlert"
    v-if="$page.props.flash.warning && showAlert"
    closable
    type="warning"
    :title="$t('flashMessages.warningTitle')"
    :text="$t($page.props.flash.warning)"
    class="mb-4"
  ></v-alert>
  <v-alert
    :value="showAlert"
    v-if="($page.props.flash.error || Object.keys($page.props.errors).length > 0) && showAlert"
    closable
    type="error"
    :title="$t('flashMessages.errorTitle')"
    :text="formatErrors($page.props.errors)"
    class="mb-4"
  ></v-alert>
</template>

<script setup>

import {onBeforeMount, onMounted, ref} from "vue";

const showAlert = ref(true);

function formatErrors(errorObj) {
  console.log(errorObj);

  let ret = '';
  Object.keys(errorObj).forEach(key => {
    ret += key + ': ' + errorObj[key] + "\n";
  });
  return ret;
}

onMounted(() => {
  setTimeout(()=>{
    showAlert.value = false;
  },3000)
});

</script>
