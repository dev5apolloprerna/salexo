        <div class="col-lg-6 col-md-6">
            <label>Name <span style="color:red;">*</span></label>
            <input type="text" name="emp_name" class="form-control"
                value="{{ old('emp_name', $employee->emp_name ?? '') }}" maxlength="100" placeholder="Enter Name" required>
        </div>

        <div class="col-lg-6 col-md-6">
            <label>Mobile <span style="color:red;">*</span></label>
            <input type="text" name="emp_mobile" class="form-control"
                value="{{ old('emp_mobile', $employee->emp_mobile ?? '') }}"
                onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g,'')" maxlength="10"
                placeholder="Enter Mobile" required>
        </div>

        <div class="col-lg-6 col-md-6">
            <label>Email <span style="color:red;"></span></label>
            <input type="email" name="emp_email" class="form-control"
                value="{{ old('emp_email', $employee->emp_email ?? '') }}" maxlength="100" placeholder="Enter Email">
        </div>

        @if (!isset($employee))
            <div class="col-lg-6 col-md-6">
                <label>Password <span style="color:red;">*</span></label>
                <input type="password" name="password" class="form-control" maxlength="10" required
                    placeholder="Enter Password">
            </div>
        @endif
