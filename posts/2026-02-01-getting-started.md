---
image: img/getting-started.jpg
---

# Getting Started with phpcoin: Your First Steps

So you've heard about phpcoin—the blockchain written in PHP. Maybe you're a PHP developer curious about crypto, or a crypto enthusiast intrigued by a PHP-based chain. Either way, this guide will get you started.

## Important: Node Requirements

To run a **phpcoin node**, you need:

- A **VPS (Virtual Private Server)** with a **public IP address**
- **PHP 7.2+** with extensions: `gmp`, `bcmath`, `curl`, `mysql` or `sqlite`
- **Web server**: Apache or Nginx
- **Database**: MySQL 8.0 or MariaDB 10

*You cannot run a full node on localhost/home PC—it needs to be reachable by other peers on the network.*

## Step 1: Set Up Your VPS

Get a VPS from any provider (DigitalOcean, Linode, AWS, Hetzner, etc.). 

**Minimum specs:**
- 1 vCPU
- 1GB RAM
- 20GB storage
- Ubuntu 20.04+ or Debian

## Step 2: Install the Node (Automatic)

The easiest way—run this on your clean VPS:

```bash
curl -s https://phpcoin.net/scripts/install_node.sh | bash
```

This script automatically:
- Installs Apache + PHP + MariaDB
- Downloads the node source from GitHub
- Configures the database
- Sets up the web server
- Starts syncing the blockchain

For **testnet**:
```bash
curl -s https://phpcoin.net/scripts/install_node.sh | bash -s -- --network testnet
```

## Step 3: Check Your Node

After installation, your node will be available at:
```
http://YOUR_VPS_IP
```

The node will start syncing the blockchain—this may take some time depending on the chain size.

## Step 4: Create Your Wallet

Once synced, create a wallet:

**Via web interface:**
```
http://YOUR_VPS_IP/apps/wallet
```

**Via API:**
```bash
curl http://YOUR_VPS_IP/api.php?q=generateAccount
```

Save your **private key** somewhere secure. Lose it, and you lose access to your funds.

## Step 5: Get Some PHP

To interact with the network, you need PHP coins:

### Option A: Buy on Exchange
Check supported exchanges on [phpcoin.net](https://phpcoin.net/).

### Option B: Mine with Your Node
Enable mining in your node config (`config/config.inc.php`):

```php
$_config['miner'] = true;
$_config['miner_public_key'] = "YOUR_PUBLIC_KEY";
$_config['miner_private_key'] = "YOUR_PRIVATE_KEY";
```

The node miner receives **30% of block reward** when it mines a block.

### Option C: Run a Generator
Enable generator to earn from connected miners:

```php
$_config['generator'] = true;
$_config['generator_public_key'] = "YOUR_PUBLIC_KEY";
$_config['generator_private_key'] = "YOUR_PRIVATE_KEY";
```

Generators receive **10% of block reward** from blocks mined by connected miners.

### Option D: Join the Community
The phpcoin community often does giveaways and airdrops. Join Discord or Telegram to learn more.

## Step 6: Make Your First Transaction

Send PHP to another address:

```bash
curl -X POST http://YOUR_VPS_IP/api.php?q=send \
  -d "destination=DESTINATION_ADDRESS" \
  -d "amount=10" \
  -d "private_key=YOUR_PRIVATE_KEY"
```

Or use the web wallet for a friendlier interface.

## Step 7: Explore Smart Contracts (Optional)

Ready to build? phpcoin Smart Contracts are just PHP classes:

```php
<?php
class Counter extends SmartContractBase {
    /** @SmartContractVar */
    public $count = 0;
    
    /** @SmartContractTransact */
    public function increment() {
        $this->count++;
        return $this->count;
    }
    
    /** @SmartContractView */
    public function getCount() {
        return $this->count;
    }
}
```

Deploy it, interact with it—it's that simple.

## Common Questions

**Q: Can I run a node on my home computer?**
A: No. A phpcoin node requires a public IP address to communicate with other nodes on the network. Use a VPS.

**Q: Do I need to run a node to use phpcoin?**
A: No. You can use web wallets or third-party services. Running your own node gives you maximum security and the ability to mine.

**Q: How much disk space does the blockchain need?**
A: As of early 2026, the full chain is under 5GB. It grows slowly—60-second blocks keep it lean.

**Q: Can I mine on a Raspberry Pi?**
A: Not effectively as a full node (needs public IP). But you can use a Pi as a **standalone miner** connecting to a generator node.

**Q: Is there a mobile wallet?**
A: Check the official site for the latest wallet options. The web wallet works on mobile browsers too.

## Manual Installation

If you prefer manual setup over the automatic script, see the full guide:
[github.com/phpcoinn/node/wiki/Node-installation](https://github.com/phpcoinn/node/wiki/Node-installation)

## Docker Option

Docker images are available:
```bash
docker pull phpcoin/node
```

More info: [hub.docker.com/u/phpcoin](https://hub.docker.com/u/phpcoin)

## Next Steps

- **Read the docs**: [phpcoin.net/docs](https://phpcoin.net/)
- **Explore the explorer**: [main1.phpcoin.net/apps/explorer](https://main1.phpcoin.net/apps/explorer/)
- **Join the community**: Discord, Telegram, Twitter
- **Build something**: Smart Contracts, dApps, tools—the ecosystem is young and needs builders

## Welcome to the Network

phpcoin isn't just a blockchain. It's a statement: **web developers deserve a seat at the crypto table.**

You don't need to learn Rust. You don't need to master Solidity. If you know PHP, you already know how to build on phpcoin.

Fire up that VPS, deploy your node, and welcome to the future of decentralized PHP.

**See you on the chain.**