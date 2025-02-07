<template>
  <v-dialog
      v-model="dialog"
      :max-width="options.width"
      :style="{ zIndex: options.zIndex }"
      @keydown.esc="cancel"
  >
    <v-card>
      <v-toolbar dark :color="options.color" dense flat>
        <v-toolbar-title class="white--text">{{ title }}</v-toolbar-title>
      </v-toolbar>
      <v-card-text v-show="!!message" class="pa-4">
        {{ message }}
        <v-textarea
          class="mt-3"
          v-model="textInput"
          auto-grow
        >
        </v-textarea>
      </v-card-text>
      <v-card-actions class="pt-0">
        <v-spacer></v-spacer>
        <v-btn text @click.native="agree">{{ $t('form.yesBtn') }}</v-btn>
        <v-btn color="grey" text @click.native="cancel">{{ $t('form.noBtn') }}</v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script setup>

import { ref } from 'vue'

const dialog = ref(false);
const resolve = ref(null);
const reject = ref(null);
const message = ref(null);
const title = ref(null);

const options = ref({
  color: 'primary',
  width: 480,
  zIndex: 200
});

const textInput = ref('');

function open(myTitle, myMessage, myOptions) {
  dialog.value = true;

  title.value = myTitle;
  message.value = myMessage;
  textInput.value = "";
  options.value = Object.assign(options.value, myOptions);

  return new Promise((myResolve, myReject) => {
    resolve.value = myResolve
    reject.value = myReject
  })
}

function agree() {
  resolve.value({value: true, text: textInput.value});
  dialog.value = false;
}

function cancel() {
  resolve.value({value: false, text: textInput.value});
  dialog.value = false;
}

defineExpose({
  open
});

</script>
