@include('user.header')
<!-- App Capsule -->
<div id="appCapsule">
    <div class="section full bg-primary">
        <!-- carousel single -->
        <div class="carousel-single splide p-2">
            <div class="splide__track">
                <ul class="splide__list">
                    <li class="splide__slide">
                        <!-- card block -->
                        <div class="card-block bg-transparent border border-info">
                            <div class="card-main">
                                <div class="balance"> <span class="label">SAVINGS</span>
                                    <h1 class="title">
                                        {{ number_format($savings_balance, 2) }} </h1>
                                </div>
                                <div class="in">
                                    <div class="card-number"> <span class="label">Account Number</span> •••• {{
                                        substr(Auth::user()->account_number, -4) }}
                                    </div>
                                    <div class="bottom">
                                        <div class="card-expiry">
                                            <span class="label">Total Credit <br> {{ $currentMonth }}</span>
                                            ${{ number_format($totalSavingsCredit, 2) }}
                                        </div>
                                        <div class="card-ccv">
                                            <span class="label">Total Debit<br> {{ $currentMonth }}</span>
                                            ${{ number_format($totalSavingsDebit, 2) }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- * card block -->
                    </li>
                    <li class="splide__slide">
                        <!-- card block -->
                        <div class="card-block bg-transparent border border-light">
                            <div class="card-main">
                                <div class="balance"> <span class="label">CHECKINGS</span>
                                    <h1 class="title">
                                        {{ number_format($checking_balance, 2) }} </h1>
                                </div>
                                <div class="in">
                                    <div class="card-number"> <span class="label">Account Number</span> •••• {{
                                        substr(Auth::user()->account_number, -4) }}
                                    </div>
                                    <div class="bottom">
                                        <div class="card-expiry">
                                            <span class="label">Total Credit <br> {{ $currentMonth }}</span>
                                            ${{ number_format($totalCheckingCredit, 2) }}
                                        </div>
                                        <div class="card-ccv">
                                            <span class="label">Total Debit<br> {{ $currentMonth }}</span>
                                            ${{ number_format($totalCheckingDebit, 2) }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <!-- * card block -->
                    </li>
                </ul>
            </div>
        </div>
        <!-- * carousel single -->
    </div>

    <div class="card">
        <div class="row">

            <div class="col-lg-8">
                <div class="section wallet-card-section mb-1">
                    <div class="wallet-card">
                        <h5 class="bg-primary p-2">
                            Wire Transfer </h5>
                        <hr>
                        <h5 class="modal-title text-primary">
                            First Secure Wire
                            Transfer<br><small><span class="text-danger">Note:</span> Wire Transactions Fee is
                                1%
                            </small>
                        </h5>
                        <hr>
                        <form method="POST" action="{{ route('transfer.process') }}">
                            @csrf
                            <input type="hidden" name="type" value="wire">
                            <input type="hidden" name="user_id" value="20">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group basic">
                                        <div class="input-wrapper">
                                            <label class="label" for="account1">From</label>
                                            <select class="form-control custom-select" name="account" required>
                                                <option value=""></option>
                                                <option value="savings" {{ old('account')=='savings' ? 'selected' : ''
                                                    }}>
                                                    Savings (***0260) - ${{ number_format($savings_balance, 2) }}
                                                </option>
                                                <option value="checking" {{ old('account')=='checking' ? 'selected' : ''
                                                    }}>
                                                    Checking (***0942) - ${{ number_format($checking_balance, 2) }}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group basic">
                                        <label class="label">Enter Amount</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text text-primary">$</span>
                                            <input type="number" name="amount" class="form-control"
                                                value="{{ old('amount') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group basic">
                                        <label class="label">Beneficiary Name</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text text-primary"><i
                                                    class="fas fa-user"></i></span>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ old('name') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group basic">
                                        <label class="label">IBAN/Account Number</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text text-primary"><i
                                                    class="fas fa-file-invoice"></i></span>
                                            <input type="text" name="acct" class="form-control"
                                                value="{{ old('acct') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group basic">
                                        <label class="label">Bank</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text text-primary"><i
                                                    class="fas fa-university"></i></span>
                                            <input type="text" name="bank" class="form-control"
                                                value="{{ old('bank') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group basic">
                                        <label class="label">Swift Code</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text text-primary"><i
                                                    class="fas fa-random"></i></span>
                                            <input type="text" name="swift" class="form-control"
                                                value="{{ old('swift') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group basic">
                                        <label class="label">Routing Transit Number</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text text-primary"><i
                                                    class="fas fa-sync-alt"></i></span>
                                            <input type="text" name="routing" class="form-control"
                                                value="{{ old('routing') }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group basic">
                                        <label class="label">PIN</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text text-primary"><i
                                                    class="fas fa-user-shield"></i></span>
                                            <input type="password" name="pin" class="form-control" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group basic">
                                        <label class="label">Bank Address (Optional)</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text text-primary"><i
                                                    class="fas fa-map-marker-alt"></i></span>
                                            <textarea class="form-control" rows="3"
                                                name="address">{{ old('address') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group basic">
                                        <label class="label">Remarks</label>
                                        <div class="input-group mb-2">
                                            <span class="input-group-text text-primary"><i
                                                    class="fas fa-edit"></i></span>
                                            <textarea class="form-control" rows="3"
                                                name="remarks">{{ old('remarks') }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group basic">
                                        <input type="submit" name="submit_transfer" value="Proceed"
                                            class="btn btn-primary btn-block">
                                    </div>
                                </div>
                            </div>
                        </form>

                        <script>
                            @if(session('success'))
                                toastr.success("{{ session('success') }}");
                            @endif
                        
                            @if(session('error'))
                                toastr.error("{{ session('error') }}");
                            @endif
                        
                            @if ($errors->any())
                                @foreach ($errors->all() as $error)
                                    toastr.error("{{ $error }}");
                                @endforeach
                            @endif
                        </script>

                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="section wallet-card-section mb-1">
                    <div class="wallet-card" id="cards">
                        <h5 class="text-primary">
                            First&nbsp;Cards
                        </h5>
                        <hr>

                        <div class="wrapper">
                            <div class="credit-card-wrap">
                                <div class="credit-card-inner">
                                    <img src="https://uniontb.online/uploads/logo.png" class="pull-right sitelogo">
                                    <div class="mk-icon-sim"></div>
                                    <div class="credit-font credit-card-number" data-text="">4716 XXXX XXXX
                                        7554 </div>
                                    <br>
                                    <footer class="footer">
                                        <div class="clearfix">
                                            <div class="pull-left">
                                                <div class="credit-card-date"><span class="title">VALID
                                                        THRU</span>
                                                    <span class="credit-font">
                                                        02/28 </span>
                                                </div>
                                                <div class="credit-font credit-author">
                                                    {{Auth::user()->name}} </div>
                                            </div>
                                            <div class="pull-right">
                                                <div class="mk-icon-visa"></div>
                                            </div>
                                        </div>
                                    </footer>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="wallet-card" id="tips">
                        <h5 class="text-primary">
                            First&nbsp;Tips
                        </h5>
                        <hr>
                        <div class="transactions">
                            <!-- item -->
                            <a href="#support" class="item">
                                <div class="detail"> <span
                                        class="fas fa-piggy-bank image-block imaged w48 text-warning"></span>
                                    <div> <strong>Auto Save</strong>
                                        <p>Set a goal, save automatically with
                                            Union Trust Bank's Auto Save and track your progress.
                                        </p>
                                    </div>
                                </div>
                            </a>
                            <!-- * item -->
                            <!-- item -->
                            <a href="#support" class="item">
                                <div class="detail"> <span
                                        class="fas fa-wallet image-block imaged w48 text-success"></span>
                                    <div> <strong>Budget</strong>
                                        <p>Check in with your budget and stay on top of your spending</p>
                                    </div>
                                </div>
                            </a>
                            <!-- * item -->
                            <!-- item -->
                            <a href="#support" class="item">
                                <div class="detail"> <span class="fas fa-home image-block imaged w48 text-info"></span>
                                    <div> <strong>Home Option</strong>
                                        <p>Your home purchase, refinance and insights right under one roof.</p>
                                    </div>
                                </div>
                            </a>
                            <!-- * item -->
                            <!-- item -->
                            <a href="#support" class="item">
                                <div class="detail"> <span
                                        class="fa fa-info-circle text-danger image-block imaged w48"></span>
                                    <div> <strong>Security Tip</strong>
                                        <p class="text-black">We will NEVER ask you to provide your security
                                            details such as COT Code or any sensitive details of your account.
                                        </p>
                                    </div>
                                </div>
                            </a>
                            <!-- * item -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@include('user.footer')