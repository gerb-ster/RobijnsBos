<template>
    <Head><title>Homepage</title></Head>
    <v-container fluid>
        <v-row>
            <v-col cols="12" md="12">
                <h3 class="mb-8">RobijnsBos Homepage</h3>
                <p>{{ event }}</p>
            </v-col>
        </v-row>
    </v-container>
</template>

<script setup>

import { Head } from '@inertiajs/vue3';

defineProps({ event: String });

</script>
