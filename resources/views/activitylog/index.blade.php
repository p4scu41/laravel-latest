<!DOCTYPE html>
<html>
<head>
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/vue"></script> --}}
</head>
<style type="text/css">
    tbody tr:hover {
        background-color: #edf2f7;
    }
    button:focus {
        outline: 0;
    }
</style>
<body class="bg-gray-100 h-screen antialiased leading-none">
    <div class="flex h-screen bg-gray-200 font-roboto">

        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-200" id="app">
            <div class="mx-auto xl:px-40 lg:px-16 md:px-10 sm:px-5 px-0 py-8">

                <div class="flex flex-col mt-8">
                    <div class="-my-2 py-2 overflow-x-auto">
                        <div class="align-middle inline-block min-w-full shadow overflow-hidden sm:rounded-lg border-b border-gray-200">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-gray-300 text-xs text-gray-600 uppercase tracking-wider border-b border-gray-500 align-baseline">
                                        <th class="p-4">id</th>
                                        <th class="p-4">
                                            log_name
                                            <select class="outline-none mt-2 p-1 rounded bg-white" v-model="filters.log_name">
                                                <option value="" selected>All</option>
                                                <option v-for="option in options.log_name" :value="option">@{{ option }}</option>
                                            </select>
                                        </th>
                                        <th class="p-4">
                                            description
                                            <select class="outline-none mt-2 p-1 rounded bg-white" v-model="filters.description">
                                                <option value="" selected>All</option>
                                                <option v-for="option in options.description" :value="option">@{{ option }}</option>
                                            </select>
                                        </th>
                                        <th class="p-4">
                                            subject_id
                                            <input type="text" size="3" name="" class="outline-none mt-2 p-1 rounded" v-model.lazy="filters.subject_id">
                                        </th>
                                        <th class="p-4">
                                            subject_type
                                            <select class="outline-none mt-2 p-1 rounded bg-white" v-model="filters.subject_type">
                                                <option value="" selected>All</option>
                                                <option v-for="option in options.subject_type" :value="option">@{{ option }}</option>
                                            </select>
                                        </th>
                                        <th class="p-4">
                                            causer_id
                                            <input type="text" size="3" name="" class="outline-none mt-2 p-1 rounded" v-model.lazy="filters.causer_id">
                                        </th>
                                        <th class="p-4">
                                            causer_type
                                            <select class="outline-none mt-2 p-1 rounded bg-white" v-model="filters.causer_type">
                                                <option value="" selected>All</option>
                                                <option v-for="option in options.causer_type" :value="option">@{{ option }}</option>
                                            </select>
                                        </th>
                                        <th class="p-4">
                                            IP
                                        </th>
                                        <th class="p-4">
                                            created_at
                                        </th>
                                        <th class="p-4"></th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white text-sm leading-5 text-gray-900">
                                    <tr class="border-b border-gray-200" v-for="user in users">
                                        <td class="p-4">@{{ user.id }}</td>
                                        <td class="p-4">@{{ user.log_name }}</td>
                                        <td class="p-4">@{{ user.description }}</td>
                                        <td class="p-4">@{{ user.subject_id }}</td>
                                        <td class="p-4">@{{ user.subject_type }}</td>
                                        <td class="p-4">@{{ user.causer_id }}</td>
                                        <td class="p-4">@{{ user.causer_type }}</td>
                                        <td class="p-4">@{{ user.properties.ip }}</td>
                                        <td class="p-4">@{{ (new Date(user.created_at)).toLocaleString() }}</td>
                                        <td class="p-4"><a href="#" class="text-indigo-600 hover:text-indigo-900">View</a></td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                    </div>

                    <ul class="table border-collapse mx-auto m-5">
                        <li class="table-cell border border-gray-300 w-12 h-12">
                            <button class="flex w-full h-full items-center justify-center font-bold bg-white text-gray-600 hover:text-gray-800 hover:bg-gray-200" @click="gotoPage(1)">«</button>
                        </li>
                        <li v-for="page in prevPages" :key="page" class="table-cell border border-gray-300 w-12 h-12">
                            <button class="flex w-full h-full items-center justify-center font-bold bg-white text-gray-600 hover:text-gray-800 hover:bg-gray-200" @click="gotoPage(page)"> @{{ page }} </button>
                        </li>
                        <li class="table-cell border border-gray-300 w-12 h-12">
                            <button class="flex w-full h-full items-center justify-center font-bold bg-white bg-gray-500 text-white"> @{{ pagination.current_page }} </button>
                        </li>
                        <li v-for="page in nextPages" :key="page" class="table-cell border border-gray-300 w-12 h-12">
                            <button class="flex w-full h-full items-center justify-center font-bold bg-white text-gray-600 hover:text-gray-800 hover:bg-gray-200" @click="gotoPage(page)"> @{{ page }} </button>
                        </li>
                        <li class="table-cell border border-gray-300 w-12 h-12">
                            <button class="flex w-full h-full items-center justify-center font-bold bg-white text-gray-600 hover:text-gray-800 hover:bg-gray-200" @click="gotoPage(pagination.last_page)">»</button>
                        </li>
                    </ul>

                </div>

            </div>
        </main>
    </div>

    <script type="text/javascript">
        var app = new Vue({
            el: '#app',
            data: {
                users: [],
                options: {},
                filters: {
                    log_name    : '',
                    description : '',
                    subject_id  : '',
                    subject_type: '',
                    causer_id   : '',
                    causer_type : '',
                },
                pagination: {
                    current_page : 1,
                    last_page    : 1,
                },
            },
            computed: {
                prevPages() {
                    let page = this.pagination.current_page - 1;
                    let iterations = 2;
                    let pages = [];

                    while (page >= 1 && iterations > 0) {
                        pages.push(page);
                        page--;
                        iterations--;
                    }

                    return pages;
                },
                nextPages() {
                    let page = this.pagination.current_page + 1;
                    let iterations = 2;
                    let pages = [];

                    while (page <= this.pagination.last_page && iterations > 0) {
                        pages.push(page);
                        page++;
                        iterations--;
                    }

                    return pages;
                }
            },
            async created () {
                await this.fetchLogs();
            },
            watch: {
                filters: {
                    deep: true,
                    handler() {
                        this.fetchLogs();
                    }
                }
            },
            methods: {
                async fetchLogs(page = 1) {
                    let response = await fetch('/api/activitylogs?page=' + page + '&' + this.parseFilters());
                    let payload  = await response.json();

                    this.users   = payload.data;
                    this.options = payload.options;
                    this.pagination = {
                        current_page : payload.current_page,
                        last_page    : payload.last_page,
                    };
                },
                parseFilters() {
                    let filters_parsed = [];

                    for(let filter in this.filters) {
                        if (this.filters[filter].length > 0) {
                            filters_parsed.push('filter[' + filter + ']=' + encodeURI(this.filters[filter]));
                        }
                    }

                    return filters_parsed.join('&');
                },
                gotoPage(page) {
                    this.fetchLogs(page);
                },
            }
        });
    </script>
</body>
</html>
