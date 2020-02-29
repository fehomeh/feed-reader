import {Injectable} from '@angular/core';
import {HttpClient} from '@angular/common/http';
import {environment} from "../../environments/environment";

@Injectable({providedIn: 'root'})
export class UserService {
  constructor(private http: HttpClient) {
  }

  register(email: string, password: string, repeatPassword: string) {
    return this.http.post(`${environment.apiUrl}users`, {
      email,
      password,
      "repeat": repeatPassword
    }, {withCredentials: true});
  }

  isEmailFree(email: string) {
    return this.http.get(`${environment.apiUrl}users/email/${email}`, {withCredentials: true});
  }
}
