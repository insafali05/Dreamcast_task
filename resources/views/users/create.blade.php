<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .error {
            color: red;
            font-size: 0.9em;
        }

        input.error,
        select.error,
        textarea.error {
            border: 1px solid red;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="text-center mb-4">User Registration</h1>

        <form id="registrationForm" method="POST" enctype="multipart/form-data" class="border p-4 bg-light rounded">
            @csrf
            <div class="row">
                <div class="col-4">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter your name" required>
                        <span class="error" id="nameError"></span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
                        <span class="error" id="emailError"></span>
                    </div>
                </div>

                <div class="col-4">
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="text" class="form-control" name="phone" id="phone" placeholder="Enter your phone number" required>
                        <span class="error" id="phoneError"></span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" name="description" id="description" rows="3" placeholder="Enter description" required></textarea>
                        <span class="error" id="descriptionError"></span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="role_id">Role:</label>
                        <select class="form-control" name="role_id" id="role_id" required>
                            <option value="" disabled selected>Select role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        <span class="error" id="roleError"></span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-group">
                        <label for="profile_image">Profile Image:</label>
                        <input type="file" class="form-control-file" name="profile_image" id="profile_image" required>
                        <span class="error" id="imageError"></span>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Register User</button>
        </form>

        <h2 class="text-center mt-5">Registered Users</h2>

        <table class="table table-bordered table-striped mt-4" id="userTable">
            <thead class="thead-dark">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Description</th>
                    <th>Role</th>
                    <th>Profile Image</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->description }}</td>
                    <td>{{ $user->role->name }}</td>
                    <td><img src="{{ Storage::url($user->profile_image) }}" alt="Profile Image" width="50" class="img-thumbnail"></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function() {

            function validateForm() {
                let isValid = true;

                $('.error').text('');
                $('input, select, textarea').removeClass('error');

                const name = $('#name').val().trim();
                if (name === '') {
                    $('#nameError').text('Name is required.');
                    $('#name').addClass('error');
                    isValid = false;
                }

                const email = $('#email').val().trim();
                const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
                if (email === '') {
                    $('#emailError').text('Email is required.');
                    $('#email').addClass('error');
                    isValid = false;
                } else if (!emailPattern.test(email)) {
                    $('#emailError').text('Please enter a valid email address.');
                    $('#email').addClass('error');
                    isValid = false;
                }

                const phone = $('#phone').val().trim();
                const phonePattern = /^[6-9]\d{9}$/;
                if (phone === '') {
                    $('#phoneError').text('Phone number is required.');
                    $('#phone').addClass('error');
                    isValid = false;
                } else if (!phonePattern.test(phone)) {
                    $('#phoneError').text('Please enter a valid 10-digit Indian phone number starting with 6, 7, 8, or 9.');
                    $('#phone').addClass('error');
                    isValid = false;
                }

                const description = $('#description').val().trim();
                if (description === '') {
                    $('#descriptionError').text('Description is required.');
                    $('#description').addClass('error');
                    isValid = false;
                }

                const roleId = $('#role_id').val();
                if (!roleId) {
                    $('#roleError').text('Role selection is required.');
                    $('#role_id').addClass('error');
                    isValid = false;
                }

                const profileImage = $('#profile_image').val();
                if (profileImage === '') {
                    $('#imageError').text('Profile image is required.');
                    $('#profile_image').addClass('error');
                    isValid = false;
                }

                return isValid;
            }


            $('#registrationForm').on('submit', function(e) {
                e.preventDefault();

                if (!validateForm()) {
                    return;
                }

                const formData = new FormData(this);

                $.ajax({
                    url: "{{ route('user.store') }}",
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        prependUserRow(response.user);
                        $('#registrationForm')[0].reset();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            alert(Object.values(errors).flat().join('\n'));
                        }
                    }
                });
            });

            function prependUserRow(user) {
                $('#userTable tbody').prepend(`
                    <tr>
                        <td>${user.name}</td>
                        <td>${user.email}</td>
                        <td>${user.phone}</td>
                        <td>${user.description}</td>
                        <td>${user.role.name}</td>
                        <td><img src="${user.profile_image}" alt="Profile Image" width="50" class="img-thumbnail"></td>
                    </tr>
                `);
            }
        });
    </script>
</body>

</html>