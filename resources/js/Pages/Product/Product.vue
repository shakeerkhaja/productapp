<template>
    <app-layout title="Products">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Products
            </h2>
        </template>

        <template #actions>
            <Button @click="createForm = true" label="New Product" icon="pi pi-plus" iconPos="left" />
        </template>



    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import Welcome from '@/Jetstream/Welcome.vue'
    import Button from 'primevue/button';



    export default defineComponent({
        components: {
            AppLayout,
            Welcome,
            Button
        },
    })
</script>
