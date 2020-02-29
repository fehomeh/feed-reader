import {Injectable} from '@angular/core';
import {HttpEvent, HttpHandler, HttpInterceptor, HttpRequest} from '@angular/common/http';
import {Observable, throwError} from 'rxjs';
import {catchError} from 'rxjs/operators';

import {AuthenticationService} from '../_services/authentication.service';
import {AlertService} from "../_services/alert.service";

@Injectable()
export class ErrorInterceptor implements HttpInterceptor {
  constructor(private authenticationService: AuthenticationService, private alertService: AlertService) {
  }

  intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    return next.handle(request).pipe(catchError(err => {
      if (err.status === 401) {
        const error = err.error.error || '';
        if (error === 'Invalid credentials.') {
          this.alertService.error(error);
          return throwError(error);
        }
        this.authenticationService.logout();
        location.reload();
      }

      const error = err.error || err.statusText;
      return throwError(error);
    }))
  }
}
