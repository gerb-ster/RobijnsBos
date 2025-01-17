<template>
    <Layout>
        <Head title="Welcome" />
        <h1>Welcome</h1>
        <p>{{ event }}</p>
    </Layout>
</template>

<script setup>

import { Head } from '@inertiajs/vue3'
import Layout from "../Shared/Layout.vue";

defineProps({ event: String })
</script>
