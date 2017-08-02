import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { AddWeightComponent } from './add-weight.component';

describe('AddWeightComponent', () => {
  let component: AddWeightComponent;
  let fixture: ComponentFixture<AddWeightComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ AddWeightComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(AddWeightComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should be created', () => {
    expect(component).toBeTruthy();
  });
});
