---
image: img/php-code.jpg
---

# What is phpcoin? A Blockchain Built with PHP

If you're reading this, you're probably curious about PHPCoin blockchain project that's doing something different. Let's start with the basics.

## A Pure PHP Blockchain

PHPCoin is a **fully functional blockchain** written entirely in PHP. That means the node software, consensus mechanism, and Smart Contract engine are all built using one of the world's most popular programming languages.

No Rust. No Go. No Solidity.

Just PHP.

### Why Does This Matter?

PHP powers over 75% of websites on the internet. It's the language behind WordPress, Laravel, Symfony, and countless other platforms. Millions of developers already know it.

But until now, PHP developers have been largely excluded from blockchain development. Most blockchain platforms require learning entirely new languages like Solidity, Rust, or specialized DSLs.

PHPCoin removes that barrier.

## Key Features

### 1. Smart Contracts in PHP

Write Smart Contracts the same way you write PHP classes:

```php
<?php
class MyToken extends SmartContractBase {
    /** @SmartContractVar */
    public $name = "MyToken";
    
    /** @SmartContractMap */
    public $balances = [];
    
    /** @SmartContractTransact */
    public function transfer($to, $amount) {
        $from = $this->sender();
        $this->balances[$from] -= $amount;
        $this->balances[$to] += $amount;
    }
}
```

If you can write a PHP class, you can write a Smart Contract on PHPCoin.

### 2. ePoW Consensus (Elapsed Proof of Work)

Unlike traditional Proof-of-Work chains where the fastest hardware wins, PHPCoin uses **ePoW**—a unique consensus mechanism that incorporates time into the difficulty calculation.

What does this mean?
- **Fairer mining**: Industrial mining farms don't have as much advantage
- **Stable block time**: 60 seconds per block
- **Decentralization**: Home miners can still participate meaningfully

### 3. Masternodes & Staking

- **Masternodes**: Collateralize 80,000 PHP to run a masternode
- **Staking**: Stake as little as 100 PHP to earn rewards
- Both mechanisms help secure the network and reward participants

## The Numbers

- **Genesis Block**: April 1, 2023
- **Total Supply**: ~203 million PHP
- **Block Time**: 60 seconds
- **Consensus**: ePoW (Argon2 + SHA256)
- **Price**: ~3,742 PHP = 1 USD (as of January 2026)

## What's Next?

We're currently preparing for a **Smart Contract relaunch** on mainnet. Over the past months, we've:
- Completed a comprehensive security audit
- Hardened the Smart Contract sandbox
- Removed critical vulnerabilities
- Validated our consensus mechanism

The platform is production-ready, and we're inviting PHP developers worldwide to join us.

## Getting Started

Want to explore phpcoin? Here's where to start:

- **Website**: [phpcoin.net](https://phpcoin.net/)
- **GitHub**: [github.com/phpcoinn/node](https://github.com/phpcoinn/node)
- **Block Explorer**: [main1.phpcoin.net/apps/explorer](https://main1.phpcoin.net/apps/explorer/)

Whether you're a PHP veteran or just getting started with blockchain, PHPCoin welcomes you.

**The future of blockchain is inclusive. And it's written in PHP.**