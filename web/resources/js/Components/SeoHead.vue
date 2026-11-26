<script setup>
import { Head } from '@inertiajs/vue3';

defineProps({
    seo: { type: Object, required: true },
});

</script>

<template>
    <Head :title="seo.title">
        <meta head-key="description" name="description" :content="seo.description" />
        <link v-if="seo.canonical" head-key="canonical" rel="canonical" :href="seo.canonical" />
        <meta head-key="og:title" property="og:title" :content="seo.title" />
        <meta head-key="og:description" property="og:description" :content="seo.description" />
        <meta head-key="og:type" property="og:type" :content="seo.type || 'website'" />
        <meta v-if="seo.canonical" head-key="og:url" property="og:url" :content="seo.canonical" />
        <meta v-if="seo.image" head-key="og:image" property="og:image" :content="seo.image" />
    </Head>
</template>
