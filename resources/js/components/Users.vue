<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdminOrAuthor()">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Users</h3>

                        <div class="card-tools">
                            <button class="btn btn-success" @click="newModal">Add new <i
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
                            <tr v-for="user in users.data" :key="user.id">
                                <td>{{ user.id }}</td>
                                <td>{{ user.name }}</td>
                                <td>{{ user.email }}</td>
                                <td>{{ user.type | textUppercase }}</td>
                                <td>{{ user.created_at | carbonDate }}</td>
                                <td>
                                    <a href="#" @click="editModal(user)">
                                        <i class="fa fa-edit blue"></i>
                                    </a>
                                    |
                                    <a href="#" @click="deleteUser(user.id)">
                                        <i class="fa fa-trash red"></i>
                                    </a>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                    <pagination :data="users" @pagination-change-page="getResults"></pagination>
                </div>
                <!-- /.card -->
            </div>
        </div>

        <div v-if="!$gate.isAdminOrAuthor()">
            <not-found></not-found>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-show="!editForm" class="modal-title" id="addNewUserForm">Add New</h5>
                        <h5 v-show="editForm" class="modal-title" id="editUserForm">Edit User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <form @submit.prevent="editForm ? updateUser() : createUser()">
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
                                        id="type"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.has('type') }"
                                >
                                    <option >Select user role</option>
                                    <option value="admin">Admin</option>
                                    <option value="user">Standard user</option>
                                    <option value="author">Author</option>
                                </select>
                                <has-error :form="form" field="type"></has-error>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                            <button v-show="!editForm" type="submit" class="btn btn-primary">Create</button>
                            <button v-show="editForm" type="submit" class="btn btn-primary">Update</button>
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
                editForm:false,
                users: {},
                form: new Form({
                    id: '',
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
            // phân trang
            getResults(page = 1) {
                axios.get('api/user?page=' + page)
                    .then(response => {
                        this.users = response.data;
                    });
            }
            ,
            newModal(){
              this.editForm = false;
              this.form.reset();
              $('#addNew').modal('show');
            },
            editModal(user){
              this.editForm = true;
              this.form.reset();
              $('#addNew').modal('show');
              this.form.fill(user);
            },
            loadUsers() {
            //    ACL , tk là admin thì mới request tới db
                if (this.$gate.isAdminOrAuthor()){
                    // data trả về gán, ({data}) => (this.users = data) tương đương với (param) => (this.users = param.data),  {data} là short hand của {data:data}, dấu => nó là return
                    axios.get("api/user").then(( {data} ) => (this.users = data));
                }
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
                this.form.post('api/user')
                .then(()=>{
                // sau khi post lên server thì gửi thông báo đến hook created
                // để phát hiện thay đổi trên server
                EventBus.$emit('AfterCreatedUser');
                toast.fire({
                  type: 'success',
                  title: 'User created in successfully'
                })

                $('#addNew').modal('hide');

                this.$Progress.finish();
                })
                .catch( ()=>{
                  this.$Progress.fail();
                  //console.log(errors);
                });

            },
            updateUser(id){
              this.$Progress.start();

              this.form.put('api/user/' + this.form.id)
              .then(() => {
                EventBus.$emit('AfterCreatedUser');

                toast.fire({
                  type: 'success',
                  title: 'User updated in successfully'
                });
                $('#addNew').modal('hide')
                this.$Progress.finish();
              })
              .catch( () => {
                this.$Progress.fail();
              });
            },
            deleteUser(id){
              swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
              }).then((result) => {
                if (result.value) {
                  // gửi request yêu cầu xóa user
                  this.form.delete('/api/user/'+ id)
                  .then(()=>{
                      swal.fire(
                        'Deleted!',
                        'Your file has been deleted.',
                        'success'
                      )
                      // gửi thông báo cho server biết user đã bị xóa
                      EventBus.$emit('AfterCreatedUser');
                  })
                  .catch(()=>{
                    swal.fire(
                      'Failed!',
                      'There are something wrong.',
                      'Warning'
                    )
                  })
                }

              })
            }
        },
        // hook
        created() {
            // nhận thông báo eventbus
            EventBus.$on('searching',() =>{
                let query = this.$parent.search;
                axios.get('api/searching?q='+query)
                .then((data) =>{
                    this.users = data.data;
                  
                })
                .catch(()=>{})
            });
            this.loadUsers();
            // nhận thông báo thay đổi dữ liệu từ methods createUser
            EventBus.$on('AfterCreatedUser',()=>{
                this.loadUsers();
            });

        }
    }
</script>
