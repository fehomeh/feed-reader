import {Component, OnDestroy, OnInit} from '@angular/core';
import {Subscription} from 'rxjs';

import {User} from '../_models/user';
import {Feed} from '../_models/feed';
import {FeedService} from "../_services/feed.service";
import {AuthenticationService} from '../_services/authentication.service';

@Component({
  selector: 'app-feed',
  templateUrl: './feed.component.html'
})
export class FeedComponent implements OnInit, OnDestroy {

  currentUser: User;
  currentUserSubscription: Subscription;
  feed: Feed[];
  mostPopularWords: object;
  title: string;
  logo: string;
  url: string;

  constructor(
    private authenticationService: AuthenticationService,
    private feedService: FeedService,
  ) {
    this.currentUserSubscription = this.authenticationService.currentUser.subscribe(user => {
      this.currentUser = user;
    });
  }

  ngOnInit() {
    this.loadFeed();
  }

  ngOnDestroy() {
    // unsubscribe to ensure no memory leaks
    this.currentUserSubscription.unsubscribe();
  }

  private loadFeed() {
    this.feedService.getAll().subscribe(feed => {
      if (feed['success']) {
        this.feed = feed['feed']['items'];
        this.mostPopularWords = feed['feed']['mostPopularWords'];
        this.title = feed['feed']['title'];
        this.logo = feed['feed']['logo'];
        this.url = feed['feed']['url'];
      }
    });
  }
}
