import {Component, OnInit} from '@angular/core';
import {Router} from '@angular/router';
import {AsyncValidatorFn, FormBuilder, FormGroup, Validators} from '@angular/forms';
import {debounceTime, distinctUntilChanged, first, map, switchMap} from 'rxjs/operators';
import {AuthenticationService} from '../_services/authentication.service';
import {UserService} from '../_services/user.service';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html'
})
export class RegisterComponent implements OnInit {
  registerForm: FormGroup;
  loading = false;
  submitted = false;

  constructor(
    private formBuilder: FormBuilder,
    private router: Router,
    private authenticationService: AuthenticationService,
    private userService: UserService
  ) {
    if (this.authenticationService.currentUserValue) {
      this.router.navigate(['/']);
    }
  }

  ngOnInit() {
    this.registerForm = this.formBuilder.group({
      email: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.required, Validators.minLength(6)]],
      passwordRepeat: ['', [Validators.required, Validators.minLength(6)]]
    }, {validator: this.checkPasswords});

    this.registerForm.controls['email'].setAsyncValidators(this.checkEmail())
  }

  checkPasswords(group: FormGroup) {
    const control = group.controls['password'];
    const matchingControl = group.controls['passwordRepeat'];

    if (control.value !== matchingControl.value) {
      matchingControl.setErrors({mustMatch: true});
    } else {
      matchingControl.setErrors(null);
    }
  }

  get form() {
    return this.registerForm.controls;
  }

  onSubmit() {
    this.submitted = true;

    // stop here if form is invalid
    if (this.registerForm.invalid) {
      return;
    }

    this.loading = true;
    this.userService.register(this.form.email.value, this.form.password.value, this.form.passwordRepeat.value)
      .pipe()
      .subscribe(
        () => {
          this.router.navigate(['/login']);
        },
        error => {
          const err = error.error || {};
          for (let field in err) {
            if (err.hasOwnProperty(field) && this.registerForm.contains(field)) {
              this.registerForm.controls[field].setErrors({validation: this.flattenErrors(err[field])})
            }
          }

          this.loading = false;
        });
  }

  checkEmail(): AsyncValidatorFn {
    return control => control.valueChanges
      .pipe(
        debounceTime(400),
        distinctUntilChanged(),
        switchMap(value => this.userService.isEmailFree(value)),
        map((data) => (data['is_free'] ? null : {'busy': true})),
        first());
  }

  private flattenErrors(errElement: string[]): string {
    let result: string = '';
    errElement.forEach(value => {
      result += value + ' ';
    });

    return result
  }
}
