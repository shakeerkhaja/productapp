<template>
    <app-layout title="Products">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Products
            </h2>
        </template>

        <template #actions>
            <a :href="route('products.create')">
            <Button label="Create Product" icon="pi pi-plus" iconPos="left" />
            </a>
        </template>


        <div class="container mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="card">
                <div class="card-body">
                    <vue-good-table mode="remote" @on-page-change="onPageChange" @on-column-filter="onColumnFilter" @on-per-page-change="onPerPageChange"
                                    :pagination-options="options" :columns="columns"
                                    :totalRows="products.meta.pagination.total"
                                    :rows="products.data"
                                    :select-options="{
                                    enabled: true,}"
                                    v-on:selected-rows-change="onSelectAll"
                                    >
                        <template #selected-row-actions>
                            <button @click="deleteAll()">Delete</button>
                        </template>
                        <template #table-row="props" >
                             <!-- Status Column -->
                            <div v-if="props.column.field === 'image'">
                                <img :src="props.row.image ? props.row.image : '' " alt="" class="w-13">
                            </div>

                            <!-- Status Column -->
                            <div v-else-if="props.column.field === 'status'">
                                <span :class="[props.row.status ? 'badge-success' : 'badge-danger', 'badge']">{{ props.row.status ? 'Active' : 'In-active' }}</span>
                            </div>

                            <!-- Action Column -->
                            <div v-else-if="props.column.field === 'actions'">
                                <Button icon="pi pi-pencil" @click="editProduct(props.row.id)" class="p-button-rounded p-button-secondary p-button-text p-mr-2"/>
                                <Button icon="pi pi-trash" class="p-button-rounded p-button-danger p-button-text" @click="deleteProduct(props.row.id)"/>
                            </div>

                            <!-- Remaining Columns -->
                            <span v-else>
                                {{props.formattedRow[props.column.field]}}
                            </span>
                        </template>
                    </vue-good-table>
                </div>
            </div>
        </div>



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
            Button,
        },
        props: {
          products: Object,
          errors: Object,
        },
        data() {
            return {
                createForm: false,
                editForm: false,
                currentId: null,
                columns: [
                    {
                        label: 'Image',
                        field: 'image',
                        sortable: false,
                        width: '12rem'
                    },
                    {
                        label: 'Name',
                        field: 'name',
                        sortable: false,
                        width: '12rem'
                    },
                    {
                        label: 'UPC',
                        field: 'upc',
                        sortable: false,
                        width: '8rem'
                    },

                    {
                        label: 'Price',
                        field: 'price',
                        sortable: false,
                        width: '7rem'
                    },


                    {
                        label: 'Status',
                        field: 'status',
                        sortable: false,
                        width: '11rem'
                    },
                    {
                        label: 'Actions',
                        field: 'actions',
                        sortable: false,
                        width: '12rem'
                    },
                ],
                options: {
                    enabled: true,
                    mode: 'pages',
                    perPage: this.products.meta.pagination.per_page,
                    setCurrentPage: this.products.meta.pagination.current_page,
                    perPageDropdown: [10, 20, 50, 100],
                    dropdownAllowAll: false,
                },
                serverParams: {
                    columnFilters: {},
                    sort: {
                        field: '',
                        type: '',
                    },
                    page: 1,
                    perPage: 10
                },
                loading: false,
                selected: [],
            }
        },

        methods: {
            deleteAll() {
                this.$confirm.require({
                    header: 'Confirm Delete',
                    message: 'Do you want to delete all the records?',
                    icon: 'pi pi-info-circle',
                    acceptClass: 'p-button-danger',
                    rejectLabel: 'Cancel',
                    acceptLabel: 'Delete',
                    accept: () => {
                        this.$inertia.post(route('products.delete_all'), {'id' : this.selected}, {
                            onSuccess: () => {
                                this.$toast.add({
                                    severity: 'info',
                                    summary: 'Confirmed',
                                    detail: 'Record deleted',
                                    life: 3000
                                });
                            },
                        });
                    },
                    reject: () => {

                    }
                });
            },
            onSelectAll(params) {
                this.selected = params.selectedRows;
            },
            updateParams(newProps) {
                this.serverParams = Object.assign({}, this.serverParams, newProps);
            },
            onPageChange(params) {
                this.updateParams({page: params.currentPage});
                this.loadItems();
            },
            onPerPageChange(params) {
                this.updateParams({perPage: params.currentPerPage});
                this.loadItems();
            },
            onSortChange(params) {
                this.updateParams({
                    sort: [{
                        type: params.sortType,
                        field: this.columns[params.columnIndex].field,
                    }],
                });
                this.loadItems();
            },
            onColumnFilter(params) {
                this.updateParams(params);
                this.serverParams.page = 1;
                this.loadItems();
            },
            getQueryParams() {
                let data = {
                    'page': this.serverParams.page,
                    'perPage': this.serverParams.perPage
                }

                for (const [key, value] of Object.entries(this.serverParams.columnFilters)) {
                    if (value) {
                        data[key] = value;
                    }
                }

                return data;
            },
            loadItems() {
                this.$inertia.get(route(route().current()), this.getQueryParams(), {
                    replace: false,
                    preserveState: true,
                    preserveScroll: true,
                });
            },
            editProduct(id) {
                this.$inertia.get(route('products.edit', {product: id}));
            },
            deleteProduct(id) {
                this.$confirm.require({
                    header: 'Confirm Delete',
                    message: 'Do you want to delete this record?',
                    icon: 'pi pi-info-circle',
                    acceptClass: 'p-button-danger',
                    rejectLabel: 'Cancel',
                    acceptLabel: 'Delete',
                    accept: () => {
                        this.$inertia.delete(route('products.destroy', {id: id}), {}, {
                            onSuccess: () => {
                                this.$toast.add({
                                    severity: 'info',
                                    summary: 'Confirmed',
                                    detail: 'Record deleted',
                                    life: 3000
                                });
                            },
                        });
                    },
                    reject: () => {

                    }
                });

            },

        }
    })
</script>
