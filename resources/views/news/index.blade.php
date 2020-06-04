@extends('layouts.app')

@section('styles')
<style media="screen">
  body{
    background-color: #E0E2DB;
  }
  h1,h2,h3{
    color: #272635;
  }
  .news-text{
    font-size: 29px;
  }
  .container{
    padding-bottom: 50px;
  }
  #queryString{
    border: none;
    border-radius: 0;
    font-size: 28px;
    background-color: transparent;
  }
  #queryString:focus, #queryString:active{
    border-bottom: 1px solid #05668D;
    box-shadow: none;
  }
  .page-link{
    color: #272635;
    border: 1px solid #05668D;
  }
  .page-item.active .page-link {
    background-color: #05668D;
    border-color: #05668D;
  }
  .card{
    cursor: pointer;
    border: 1px solid #05668D;
  }
</style>
@endsection

@section('content')
  <div class="container mt-4">
    <div class="row">
      <div class="col-12">
        <div class="form-group row">
          <label for="news" class="col-sm-4 col-md-2 col-lg-1 col-form-label news-text">News</label>
          <div class="col-sm-10 col-md-10 col-lg-11">
            <input id="queryString" type="text" class="form-control" placeholder="write to look for news">
          </div>
        </div>
      </div>
      <div class="col-12 text-right">
        <hr>
        <nav aria-label="News navigation">
          <ul class="pagination justify-content-end" id="pagination">
            <!-- Pagination contetn here -->
          </ul>
        </nav>
      </div>
      <div class="col-12">
        <div class="card-columns" id="news">
          <!-- New content here -->
        </div>
      </div>
    </div>
  </div>
@endsection

@section('modals')
<div class="modal fade bd-example-modal-lg" id="newsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h2 class="modal-title" id="title"></h2>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <img src="" alt="" class="img-fluid mb-2" id="image">
        <h4 id="author" class="mb-4 text-left"></h4>
        <p id="content" class="mb-2 text-left"></p>
        <h5 id="date" class="mt-4 text-left"></h5>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">

  $(document).ready(function(){
    news.init();
  });

  var news = {
    apiKey: '6df1d231b32341ae992ca6b2e5b32f75',
    queryString: $('#queryString'),
    newsContent : $(".card-columns#news"),
    pagination: $("#pagination"),
    newsModal: $("#newsModal"),
    currentPage: 1,
    currentNews: {
      title: $("#title"),
      image: $("#image"),
      content: $("#content"),
      author: $("#author"),
      date: $("#date")
    },
    news: [],
    randomQueryStrings: [
      'usa', 'covid', 'football', 'videogames', 'mexico', 'movies', 'time', 'technology', 'smartphones', 'apple', 'microsoft'
    ],
    init(){
      _self = this;

      firstNews = _self.randomQueryStrings[Math.floor(Math.random()*_self.randomQueryStrings.length)];
      _self.queryString.val(firstNews);
      _self.getNews();

      _self.queryString.keyup(function(e){
        _self.queryString.val($(this).val());
        _self.getNews();
      });

    },
    getNews(){
      _self = this;
      _self.news = [];
      query = _self.queryString.val().trim() == '' ? 'world' : _self.queryString.val().trim();
      _self.newsContent.html('Loading ... ');
      $.getJSON('https://newsapi.org/v2/everything?q='+query+'&sortBy=publishedAt&pageSize=10&page='+_self.currentPage+'&apiKey='+_self.apiKey)
        .done(function(data){
          if(data.status == 'ok'){
            _self.news = data.articles;
            _self.loadPagination(data.totalResults);
            _self.loadNews();
          }else{
            _self.newsContent.html('Error')
          }
        })
    },
    loadNews(){
      _self = this;
      _self.newsContent.html('');

      _self.news.forEach((article, i) => {
        _self.newsContent.append(`
          <div class="card" data-index="${i}">
            <img class="card-img-top" src="${article.urlToImage}" alt="${article.title} image">
            <div class="card-body">
              <h5 class="card-title">
                ${article.title}
                <br>
                <small>${article.author}</small>
              </h5>
              <p class="card-text">${article.description}</p>
              <p class="card-text"><small class="text-muted">${article.publishedAt}</small></p>
            </div>
          </div>
          `)
      });
      _self.loadNewsEvents();
    },
    loadPagination(total){
      _self = this;
      _self.pagination.html('');
      total = Math.trunc(total / 10);
      var pages = (total >= 10) ? 10 : total;
      var not_pagination = false;
      for (var i = 1; i <= pages; i++) {
        _self.pagination.append(_self.getLink(i));
      }
      _self.loadPaginationEvents();
    },
    getLink(page){
      _self = this;
      if(page == _self.currentPage){
        return `<li class="page-item active"><span class="page-link" data-page="${page}" href="#">
            ${page} <span class="sr-only">(current)</span>
          </span></li>`
      }
      return `<li class="page-item"><a class="page-link" data-page="${page}" href="#">${page}</a></li>`
    },
    loadNewsEvents(){
      _self = this;
      $(".card").click(function(){
        selected = _self.news[$(this).attr('data-index')];
        _self.currentNews.title.html(selected.title);
        _self.currentNews.image.attr('src', selected.urlToImage);
        _self.currentNews.author.html("By " + selected.author);
        _self.currentNews.content.html(selected.content);
        _self.currentNews.date.html(selected.publishedAt);

        _self.newsModal.modal('toggle');
      });
    },
    loadPaginationEvents(){
      _self = this;
      $(".page-link").click(function(){
        _self.currentPage = $(this).attr('data-page');
        _self.getNews();
      });
    }
  }
</script>
@endsection
