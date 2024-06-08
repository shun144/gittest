
@extends('adminlte::master')

<div id="container">


    <div class="header header-pc">
        <div class="header-new">
            <div class="logo-pc">
                <h1 class="pc">{{$store->name}}</h1>
            </div>
        </div>
    </div>


    <div id="content">
        <div id="inner-content">
            <main id="main">
                <article id="entry">
                    <section class="entry-content">
                        <h2 id="line-connect">LINE連携方法</h2>
                        <div>
                            <div class="sgb-tl">
                                <div class="sgb-tl-item">
                                    <div class="sgb-tl-item__label--default"></div>
                                    <div class="sgb-tl-item__title">LINE連携ボタンをクリックします。</div>
                                    <div class="sgb-tl-item__main">                                
                                        <form class="mb-0" id='lineAuthForm' action="{{ route('line.auth', ['url_name'=>$store->url_name]) }}" method="get" enctype="multipart/form-data">                    
                                            <input type="hidden" name="url_name" value="{{ $store->url_name}}" />
                                            <input type="hidden" name="client_id" value="{{ $store->client_id}}" />
                                        </form>
                                        <button form='lineAuthForm' type="submit">LINE連携</button>
                                    </div>
                                    <div class="sgb-tl-item__marker main-bdr main-bc"></div>
                                </div>

                                <div class="sgb-tl-item">
                                    <div class="sgb-tl-item__label--default"></div>
                                    <div class="sgb-tl-item__title">表示画面の「1:1でLINE Notifyから通知を受け取る」を選択します。</div>
                                    <div class="sgb-tl-item__main">
                                        <figure>
                                            <img decoding="async" src="{{ url(config('storage.user.image.entry').'/select_group.png')}}" alt="表示画面の1:1でLINE Notifyから通知を受け取るを選択">
                                        </figure>
                                    </div>
                                    <div class="sgb-tl-item__marker main-bdr main-bc"></div>
                                </div>

                                <div class="sgb-tl-item">
                                    <div class="sgb-tl-item__label--default"></div>
                                    <div class="sgb-tl-item__title">同意して連携するをクリックします。</div>
                                    <div class="sgb-tl-item__main">
                                        <figure>
                                            <img decoding="async" src="{{ url(config('storage.user.image.entry').'/agree.png')}}" alt="同意して連携するをクリック">
                                        </figure>
                                    </div>
                                    <div class="sgb-tl-item__marker main-bdr main-bc"></div>
                                </div>

                                <div class="sgb-tl-item">
                                    <div class="sgb-tl-item__label--default"></div>
                                    <div class="sgb-tl-item__title">LINEのログイン画面が表示された場合はメールアドレスとパスワードを入力しログインします。</div>
                                    <div class="sgb-tl-item__main">
                                        <figure>
                                            <img decoding="async" src="{{ url(config('storage.user.image.entry').'/line_login_pick.png')}}" alt="LINEのログイン画面が表示された場合はメールアドレスとパスワードを入力しログインします。">
                                        </figure>
                                    </div>
                                    <div class="sgb-tl-item__marker main-bdr main-bc"></div>
                                </div>

                                <div class="sgb-tl-item">
                                    <div class="sgb-tl-item__label--default"></div>
                                    <div class="sgb-tl-item__title">連携が完了すると、連携完了メッセージが通知されます。配信をお待ちください。</div>
                                    <div class="sgb-tl-item__main">
                                        <figure>
                                            <img decoding="async" src="{{ url(config('storage.user.image.entry').'/link_msg.png')}}" alt="連携が完了すると、連携完了メッセージが通知されます。配信をお待ちください。">
                                        </figure>
                                    </div>
                                    <div class="sgb-tl-item__marker main-bdr main-bc"></div>
                                </div>
                            </div>
                        </div>

                        
                        <h2 id="about">LINE通知について</h2>

                        {{-- <h2 class="wp-block-heading">
                            <span id="bbb" ez-toc-data-id="#LINE通知について"></span>LINE通知について
                        </h2> --}}
                        <div class="sgb-tl-item__title">LINE Notifyの仕組みを利用しているため、メッセージは<span>LINE Notify</span>から通知されます。</div>
                        <figure>
                            <img decoding="async" src="{{ url(config('storage.user.image.entry').'/notify.png')}}" alt="LINE通知">
                        </figure>


                        <h2 id="line-disconnect">LINE連携の解除</h2>
                        {{-- <h2 class="wp-block-heading">
                            <span id="bbb" ez-toc-data-id="#LINE連携の解除"></span>LINE連携の解除
                        </h2> --}}
                        <div>
                            <div class="sgb-tl">

                                <div class="sgb-tl-item">
                                    <div class="sgb-tl-item__label--default"></div>
                                    <div class="sgb-tl-item__title"><a href="https://notify-bot.line.me/my/">LINE Notifyマイページ</a>へアクセスします。</div>
                                    <div class="sgb-tl-item__main"></div>
                                    <div class="sgb-tl-item__marker main-bdr main-bc"></div>
                                </div>


                                <div class="sgb-tl-item">
                                    <div class="sgb-tl-item__label--default"></div>
                                    <div class="sgb-tl-item__title">解除したいサービスの<span>解除ボタン</span>をクリックします。</div>
                                    <div class="sgb-tl-item__main">                                        
                                        <figure>
                                            <img decoding="async" src="{{ url(config('storage.user.image.entry').'/lift_phone.png')}}" alt="連携が完了すると、連携完了メッセージが通知されます。配信をお待ちください。">
                                        </figure>
                                    </div>
                                    <div class="sgb-tl-item__marker main-bdr main-bc"></div>
                                </div>


                                <div class="sgb-tl-item">
                                    <div class="sgb-tl-item__label--default"></div>
                                    <div class="sgb-tl-item__title">連携解除が完了すると、連携解除完了メッセージが通知されます。</div>
                                    <div class="sgb-tl-item__main">                                        
                                        <figure>
                                            <img decoding="async" src="{{ url(config('storage.user.image.entry').'/unlink_msg.png')}}" alt="連携が完了すると、連携完了メッセージが通知されます。配信をお待ちください。">
                                        </figure>
                                    </div>
                                    <div class="sgb-tl-item__marker main-bdr main-bc"></div>
                                </div>
                            </div>
                        </div>
                    </section>
                </article>
            </main>
            <div id="side-menu">
                <div class="ez-title"><span>目次</span></div>
                <ol>
                    <li><a href="#line-connect">LINE連携方法</a></li>
                    <li><a href="#about">LINE通知について</a></li>
                    <li><a href="#line-disconnect">LINE連携の解除</a></li>
                </ol>
            </div>
        </div>

    </div>
    <footer class="footer">
        <p>{{$store->name}}</p>
    </footer>

</div>



@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('plugins/toastr/css/2.1.4/toastr.min.css')}}">
    <link rel="stylesheet" href="{{ asset('build/assets/entry.css')}}">
@stop

@section('adminlte_js')
    <script src="{{ asset('vendor/adminlte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/toastr/js/2.1.4/toastr.min.js')}}"></script>
    <script>
        @if (session('success_flash_message'))
        $(function () {toastr.success('{{ session('success_flash_message') }}');});
        @endif
        @if (session('error_flash_message'))
        $(function () {toastr.success('{{ session('error_flash_message') }}');});
        @endif
    </script>
@stop
