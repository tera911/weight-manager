import { WeightAnalyzerPage } from './app.po';

describe('weight-analyzer App', () => {
  let page: WeightAnalyzerPage;

  beforeEach(() => {
    page = new WeightAnalyzerPage();
  });

  it('should display welcome message', () => {
    page.navigateTo();
    expect(page.getParagraphText()).toEqual('Welcome to app!');
  });
});
