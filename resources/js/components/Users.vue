<template>
    <div class="container">
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Users</h3>

                        <div class="card-tools">
                            <button class="btn btn-success" data-toggle="modal" data-target="#addNew">Add new <i
                                    class="fas fa-user-plus fa-fw"></i></button>
                        </div>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tbody>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Created at</th>
                                <th>Modify</th>

                            </tr>
                            <tr v-for="user in users" :key="user.id">
                                <td>{{ user.id }}</td>
                                <td>{{ user.name }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ user.type | textUppercase }}</td>
                                <td>{{ user.created_at | carbonDate }}</td>
                                <td>
                                    <a href="#">
                                        <i class="fa fa-edit blue"></i>
                                    </a>
                                    |
                                    <a href="#">
                                        <i class="fa fa-trash red"></i>
                                    </a>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Add New</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form @submit.prevent="createUser">
                        <div class="modal-body">
                            <div class="form-group">
                                <input
                                        v-model="form.name"
                                        type="text" name="name"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('name') }"
                                        placeholder="name"
                                >
                                <has-error :form="form" field="name"></has-error>
                            </div>

                            <div class="form-group">
                                <input
                                        v-model="form.email"
                                        type="email" name="email"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('email') }"
                                        placeholder="Email Address"
                                >
                                <has-error :form="form" field="email"></has-error>
                            </div>

                            <div class="form-group">
                                <input
                                        v-model="form.bio"
                                        type="text" name="bio"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('bio') }"
                                        placeholder="Short bio for user (option)"
                                >
                                <has-error :form="form" field="bio"></has-error>
                            </div>

                            <div class="form-group">
                                <input
                                        v-model="form.password"
                                        type="password" name="password"
                                        class="form-control"
                                        id="password"
                                        :class="{ 'is-invalid': form.errors.has('password') }"
                                        placeholder="Password"
                                >
                                <has-error :form="form" field="password"></has-error>
                            </div>

                            <div class="form-group">
                                <select
                                        v-model="form.type"
                                        name="type"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('type') }"
                                >
                                    <option value="">Select user role</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">Standard user</option>
                                    <option value="author">Author</option>
                                </select>
                                <has-error :form="form" field="type"></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Create</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                users: {},
                form: new Form({
                    name: '',
                    email: '',
                    password: '',
                    bio: '',
                    type: '',
                    photo: ''
                })
            }
        },
        methods: {
            loadUsers() {
                // data trả về gán, ({data}) => (this.users = data) tương đương với (param) => (this.users = param.data)
                axios.get("api/user").then(( {data} ) => (this.users = data.data));
            },
            createUser() {
                // route api resource default
                /*
                  bước 1 hiện thanh progress
                  bước 2 bắt đầu post dữ liệu
                  bước 3 hoàn thành thì hiện thông báo toast
                  bước 4 ẩn form
                  bước 5 hoàn thành progress
                */
                this.$Progress.start();

                this.form.post('api/user');

                toast.fire({
                  type: 'success',
                  title: 'User created in successfully'
                })

                $('#addNew').modal('hide')

                  this.$Progress.finish();
                    
            }
        },
        created() {

            this.loadUsers();

        }
    }
</script>
