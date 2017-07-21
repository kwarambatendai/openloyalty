export default class EditableMap {
    constructor($filter, DataService) {
        this.config = window.OpenLoyaltyConfig;
        this.$filter = $filter;
        this.DataService = DataService;
    }

    customer(data) {
        let self = this;

        if (!data.company) {
            data.company = {}
        }

        if (!data.address) {
            data.address = {}
        }

        if (data.plainPassword) {
            return {
                address: {
                    address1: data.address.address1,
                    address2: data.address.address2,
                    city: data.address.city,
                    country: data.address.country,
                    postal: data.address.postal,
                    province: data.address.province,
                    street: data.address.street
                },
                company: {
                    name: data.company.name,
                    nip: data.company.nip
                },
                birthDate: moment(data.birthDate).format(self.config.dateFormat),
                email: data.email,
                firstName: data.firstName,
                gender: data.gender,
                lastName: data.lastName,
                phone: data.phone,
                loyaltyCardNumber: data.loyaltyCardNumber,
                plainPassword: data.plainPassword,
                agreement1: data.agreement1,
                agreement2: data.agreement2,
                agreement3: data.agreement3
            }
        } else {
            return {
                address: {
                    address1: data.address.address1,
                    address2: data.address.address2,
                    city: data.address.city,
                    country: data.address.country,
                    postal: data.address.postal,
                    province: data.address.province,
                    street: data.address.street
                },
                company: {
                    name: data.company.name,
                    nip: data.company.nip
                },
                birthDate: moment(data.birthDate).format(self.config.dateFormat),
                email: data.email,
                firstName: data.firstName,
                gender: data.gender,
                lastName: data.lastName,
                phone: data.phone,
                posId: data.posId,
                levelId: data.levelId,
                loyaltyCardNumber: data.loyaltyCardNumber,
                agreement1: data.agreement1,
                agreement2: data.agreement2,
                agreement3: data.agreement3
            }
        }
    }

    settings(data) {
        let res = _.clone(data);

        return res
    }

    humanizeCustomer(data) {
        let self = this;
        if (data.birthDate) {
            data.birthDate = moment(data.birthDate).format(self.config.dateFormat);
        }
        if (data.address) {
            data.address = _.pickBy(data.address);
        }

        return data;
    }

    humanizeUser(data) {
        return data;
    }

    level(data) {
        let self = this;
        let specialRewards = [];

        if (data.specialRewards) {
            specialRewards = _.map(data.specialRewards, function (reward) {
                return {
                    active: reward.active,
                    code: reward.code,
                    endAt: moment(reward.endAt).format(self.config.dateFormat),
                    startAt: moment(reward.startAt).format(self.config.dateFormat),
                    name: reward.name,
                    value: self.$filter('commaToDot')(reward.value)
                }
            })
        }

        return {
            conditionValue: self.$filter('commaToDot')(data.conditionValue),
            description: data.description,
            name: data.name,
            minOrder: data.minOrder,
            active: data.active,
            reward: {
                name: data.reward.name,
                value: self.$filter('commaToDot')(data.reward.value),
                code: data.reward.code
            },
            specialRewards: specialRewards
        }
    }

    humanizeLevel(data) {
        let self = this;

        if (data.reward) {
            data.reward.value = self.$filter('percent')(self.$filter('commaToDot')(data.reward.value));
        }

        if (data.specialRewards) {
            data.specialRewards = _.map(data.specialRewards, function (reward) {
                return {
                    active: reward.active,
                    code: reward.code,
                    endAt: moment(reward.endAt).format(self.config.dateFormat),
                    startAt: moment(reward.startAt).format(self.config.dateFormat),
                    name: reward.name,
                    value: self.$filter('percent')(self.$filter('commaToDot')(reward.value))
                }
            })
        }

        return data;
    }

    newEarningRule(data, deleteType) {
        let res = _.clone(data);
        delete res.usageUrl;
        switch (res.type) {
            case 'points' :
                delete res.eventName;
                delete res.skuIds;
                delete res.pointsAmount;
                delete res.multiplier;
                delete res.limit;
                delete res.rewardType;
                break;
            case 'event' :
                delete res.excludedSKUs;
                delete res.pointValue;
                delete res.excludedLabels;
                delete res.excludeDeliveryCost;
                delete res.minOrderValue;
                delete res.skuIds;
                delete res.multiplier;
                delete res.limit;
                delete res.rewardType;
                break;
            case 'custom_event' :
                delete res.excludedSKUs;
                delete res.pointValue;
                delete res.excludedLabels;
                delete res.excludeDeliveryCost;
                delete res.minOrderValue;
                delete res.skuIds;
                delete res.multiplier;
                delete res.rewardType;
                if (res.limit && !res.limit.active) {
                    delete res.limit.period;
                    delete res.limit.limit;
                }
                break;
            case 'referral' :
                delete res.excludedSKUs;
                delete res.pointValue;
                delete res.excludedLabels;
                delete res.excludeDeliveryCost;
                delete res.minOrderValue;
                delete res.skuIds;
                delete res.multiplier;
                delete res.limit;
                delete res.limit;
                break;
            case 'product_purchase' :
                delete res.excludedSKUs;
                delete res.pointValue;
                delete res.excludedLabels;
                delete res.excludeDeliveryCost;
                delete res.minOrderValue;
                delete res.eventName;
                delete res.multiplier;
                delete res.limit;
                delete res.rewardType;
                break;
            case 'multiply_for_product' :
                delete res.excludedSKUs;
                delete res.pointValue;
                delete res.excludedLabels;
                delete res.excludeDeliveryCost;
                delete res.minOrderValue;
                delete res.eventName;
                delete res.pointsAmount;
                delete res.limit;
                delete res.rewardType;
                break;
            default:
                break;
        }

        if (res.allTimeActive) {
            delete res.startAt;
            delete res.endAt;
        } else {
            delete res.allTimeActive;
            if (res.startAt) {
                res.startAt = new moment(res.startAt).format('YYYY-MM-DDTHH:mm:ssZ');
            }
            if (res.endAt) {
                res.endAt = new moment(res.endAt).format('YYYY-MM-DDTHH:mm:ssZ');
            }
        }

        if (!res.active) {
            delete res.active;
        }

        if (res.excludedSKUs) {
            let SKUs = '';
            for (let sku in res.excludedSKUs) {
                SKUs += res.excludedSKUs[sku] + ';';
            }
            if (SKUs.charAt(SKUs.length - 1) == ';') {
                SKUs = SKUs.substring(0, SKUs.length - 1)
            }
            res.excludedSKUs = SKUs;
        }

        if (res.excludedLabels) {
            let labels = '';
            for (let label in res.excludedLabels) {
                labels += res.excludedLabels[label].key + ':' + res.excludedLabels[label].value + ';';
            }
            if (labels.charAt(labels.length - 1) == ';') {
                labels = labels.substring(0, labels.length - 1)
            }
            res.excludedLabels = labels;
        }
        if (res.labels) {
            let labels = '';
            for (let label in res.labels) {
                labels += res.labels[label].key + ':' + res.labels[label].value + ';';
            }
            if (labels.charAt(labels.length - 1) == ';') {
                labels = labels.substring(0, labels.length - 1)
            }
            res.labels = labels;
        }

        delete res.earningRuleId;
        delete res.fromServer;
        delete res.restangularized;
        delete res.route;
        delete res.usages;
        delete res.levelNames;
        delete res.segmentNames;
        if (deleteType) {
            delete res.type;
        }

        return _.pickBy(res);
    }

    earningRule(data) {
        let res = _.omit(data, ['earningRuleId', 'type']);

        return res;
    }

    humanizeEarningRuleFields(data) {
        let self = this;

        if (data.startAt) {
            data.startAt = moment(data.startAt).format(self.config.dateTimeFormat)
        }
        if (data.endAt) {
            data.endAt = moment(data.endAt).format(self.config.dateTimeFormat)
        }

        if (data.levels && data.levels.length) {
            data.target = 'level'
        }

        if (data.segments && data.segments.length) {
            data.target = 'segment'
        }

        data.excludedLabels = _.pickBy(data.excludedLabels);
        if (data.excludedSKUs) {
            data.excludedSKUs = data.excludedSKUs.filter(function (e) {
                return e
            });
        }

        return data;
    }

    pos(data) {
        let res = _.clone(data);
        if (res.location.country && res.location.country.code) {
            res.location.country = res.location.country.code
        }

        delete res.currency;
        delete res.posId;
        delete res.transactionValue;
        delete res.transactionsCount;

        return _.pickBy(res);
    }

    humanizePos(data) {
        let self = this;
        let pos = _.clone(data);

        if (pos.location && pos.location.geoPoint) {
            pos.location.lat = pos.location.geoPoint.lat;
            pos.location.long = pos.location.geoPoint.long;
            delete pos.location.geoPoint;
        }

        return _.pickBy(pos)
    }

    segment(data) {
        let self = this;
        let segment = angular.copy(data);

        segment.parts = _.each(segment.parts, part => {
            delete part.segmentPartId;
            _.each(part.criteria, criterium => {
                delete criterium.criterionId;
                if (criterium.posIds) {
                    let ids = [];
                    _.each(criterium.posIds, pos => {
                        ids.push(pos.posId);
                    });
                    criterium.posIds = ids;
                }
                if (criterium.fromDate && criterium.toDate) {
                    criterium.fromDate = new moment(criterium.fromDate).format('YYYY-MM-DDTHH:mm:ssZ');
                    criterium.toDate = new moment(criterium.toDate).format('YYYY-MM-DDTHH:mm:ssZ');
                }
                let c = {};
                switch (criterium.type) {
                    case 'bought_in_pos' :
                        delete criterium.min;
                        delete criterium.max;
                        delete criterium.fromDate;
                        delete criterium.toDate;
                        delete criterium.labels;
                        delete criterium.makers;
                        delete criterium.anniversaryType;
                        delete criterium.days;
                        delete criterium.skuIds;
                        delete criterium.fromAmount;
                        delete criterium.toAmount;
                        delete criterium.percent;
                        delete criterium.posId;
                        break;
                    case 'transaction_count' :
                        delete criterium.posIds;
                        delete criterium.fromDate;
                        delete criterium.toDate;
                        delete criterium.labels;
                        delete criterium.makers;
                        delete criterium.anniversaryType;
                        delete criterium.days;
                        delete criterium.skuIds;
                        delete criterium.fromAmount;
                        delete criterium.toAmount;
                        delete criterium.percent;
                        delete criterium.posId;
                        break;
                    case 'purchase_period' :
                        delete criterium.min;
                        delete criterium.posIds;
                        delete criterium.max;
                        delete criterium.labels;
                        delete criterium.makers;
                        delete criterium.anniversaryType;
                        delete criterium.days;
                        delete criterium.skuIds;
                        delete criterium.fromAmount;
                        delete criterium.toAmount;
                        delete criterium.percent;
                        delete criterium.posId;
                        break;
                    case 'bought_labels' :
                        delete criterium.min;
                        delete criterium.posIds;
                        delete criterium.fromDate;
                        delete criterium.toDate;
                        delete criterium.max;
                        delete criterium.makers;
                        delete criterium.anniversaryType;
                        delete criterium.days;
                        delete criterium.skuIds;
                        delete criterium.fromAmount;
                        delete criterium.toAmount;
                        delete criterium.percent;
                        delete criterium.posId;
                        break;
                    case 'bought_makers' :
                        delete criterium.min;
                        delete criterium.posIds;
                        delete criterium.fromDate;
                        delete criterium.toDate;
                        delete criterium.labels;
                        delete criterium.max;
                        delete criterium.anniversaryType;
                        delete criterium.days;
                        delete criterium.skuIds;
                        delete criterium.fromAmount;
                        delete criterium.toAmount;
                        delete criterium.percent;
                        delete criterium.posId;
                        break;
                    case 'anniversary' :
                        delete criterium.min;
                        delete criterium.posIds;
                        delete criterium.fromDate;
                        delete criterium.toDate;
                        delete criterium.labels;
                        delete criterium.makers;
                        delete criterium.max;
                        delete criterium.skuIds;
                        delete criterium.fromAmount;
                        delete criterium.toAmount;
                        delete criterium.percent;
                        delete criterium.posId;
                        break;
                    case 'last_purchase_n_days_before' :
                        delete criterium.min;
                        delete criterium.posIds;
                        delete criterium.fromDate;
                        delete criterium.toDate;
                        delete criterium.labels;
                        delete criterium.makers;
                        delete criterium.anniversaryType;
                        delete criterium.max;
                        delete criterium.skuIds;
                        delete criterium.fromAmount;
                        delete criterium.toAmount;
                        delete criterium.percent;
                        delete criterium.posId;
                        break;
                    case 'bought_skus' :
                        delete criterium.min;
                        delete criterium.posIds;
                        delete criterium.fromDate;
                        delete criterium.toDate;
                        delete criterium.labels;
                        delete criterium.makers;
                        delete criterium.anniversaryType;
                        delete criterium.days;
                        delete criterium.max;
                        delete criterium.fromAmount;
                        delete criterium.toAmount;
                        delete criterium.percent;
                        delete criterium.posId;
                        break;
                    case 'transaction_amount' :
                        delete criterium.min;
                        delete criterium.posIds;
                        delete criterium.fromDate;
                        delete criterium.toDate;
                        delete criterium.labels;
                        delete criterium.makers;
                        delete criterium.anniversaryType;
                        delete criterium.days;
                        delete criterium.skuIds;
                        delete criterium.max;
                        delete criterium.percent;
                        delete criterium.posId;
                        break;
                    case 'average_transaction_amount' :
                        delete criterium.min;
                        delete criterium.posIds;
                        delete criterium.fromDate;
                        delete criterium.toDate;
                        delete criterium.labels;
                        delete criterium.makers;
                        delete criterium.anniversaryType;
                        delete criterium.days;
                        delete criterium.skuIds;
                        delete criterium.max;
                        delete criterium.percent;
                        delete criterium.posId;
                        break;
                    case 'transaction_percent_in_pos' :
                        delete criterium.min;
                        delete criterium.posIds;
                        delete criterium.fromDate;
                        delete criterium.toDate;
                        delete criterium.labels;
                        delete criterium.makers;
                        delete criterium.anniversaryType;
                        delete criterium.days;
                        delete criterium.skuIds;
                        delete criterium.max;
                        delete criterium.fromAmount;
                        delete criterium.toAmount;
                        break;
                    default:
                        break;
                }
            });
        });

        delete segment.createdAt;
        delete segment.segmentId;
        delete segment.customersCount;

        return segment;
    }

    humanizeSegment(data, pos) {
        let self = this;
        let segment = data;

        segment.parts = _.each(segment.parts, part => {
            _.each(part.criteria, criterium => {
                if (criterium.type === 'transaction_percent_in_pos') {
                    criterium.percent = self.$filter('percent')(self.$filter('commaToDot')(criterium.percent))
                }
                if (criterium.type === 'purchase_period') {
                    criterium.fromDate = moment(criterium.fromDate).format(self.config.dateTimeFormat)
                    criterium.toDate = moment(criterium.toDate).format(self.config.dateTimeFormat)
                }
            });
        });

        return segment
    }

    seller(data) {
        let self = this;
        let seller = angular.copy(data);
        let posArr = {};
        delete seller.deleted;
        delete seller.sellerId;
        delete seller.posCity;
        delete seller.posName;

        return seller;
    }

    humanizeSeller(data, pos) {
        let self = this;

        return data;
    }

    humanizeCampaign(data) {
        let self = this;
        let campaign = angular.copy(data);


        if (campaign.campaignActivity) {
            campaign.campaignActivity.activeTo = moment(campaign.campaignActivity.activeTo).format(self.config.dateTimeFormat);
            campaign.campaignActivity.activeFrom = moment(campaign.campaignActivity.activeFrom).format(self.config.dateTimeFormat)
        }

        if (campaign.campaignVisibility) {
            campaign.campaignVisibility.visibleTo = moment(campaign.campaignVisibility.visibleTo).format(self.config.dateTimeFormat);
            campaign.campaignVisibility.visibleFrom = moment(campaign.campaignVisibility.visibleFrom).format(self.config.dateTimeFormat)
        }

        if (campaign.levels && campaign.levels.length) {
            campaign.target = 'level'
        }

        if (campaign.segments && campaign.segments.length) {
            campaign.target = 'segment'
        }

        return campaign;
    }

    campaign(data) {
        let self = this;
        let campaign = angular.copy(data);

        if (campaign.campaignActivity) {
            if (campaign.campaignActivity.activeTo) {
                campaign.campaignActivity.activeTo = moment(campaign.campaignActivity.activeTo).format('YYYY-MM-DDTHH:mm:ssZ');
            }
            if (campaign.campaignActivity.activeFrom) {
                campaign.campaignActivity.activeFrom = moment(campaign.campaignActivity.activeFrom).format('YYYY-MM-DDTHH:mm:ssZ')
            }
        }

        if (campaign.campaignVisibility) {
            if (campaign.campaignVisibility.visibleTo) {
                campaign.campaignVisibility.visibleTo = moment(campaign.campaignVisibility.visibleTo).format('YYYY-MM-DDTHH:mm:ssZ');
            }
            if (campaign.campaignVisibility.visibleFrom) {
                campaign.campaignVisibility.visibleFrom = moment(campaign.campaignVisibility.visibleFrom).format('YYYY-MM-DDTHH:mm:ssZ')
            }
        }

        if (campaign.couponsCsv && campaign.couponsCsv.length) {
            if (campaign.coupons instanceof Array) {
                campaign.coupons = campaign.coupons.concat(campaign.couponsCsv)
            } else {
                campaign.coupons = campaign.couponsCsv;
            }
            campaign.coupons = _.pickBy(campaign.coupons);
        }
        delete campaign.campaignId;
        if (campaign.will_be_active_from) {
            delete campaign.will_be_active_from;
        }
        if (campaign.will_be_active_to) {
            delete campaign.will_be_active_to;
        }
        delete campaign.couponsCsv;
        delete campaign.id;
        delete campaign.segmentNames;
        delete campaign.levelNames;
        delete campaign.usageLeft;
        delete campaign.usageLeftForCustomer;
        delete campaign.usersWhoUsedThisCampaignCount;
        delete campaign.visibleForCustomersCount;

        return campaign;
    }
}

EditableMap.$inject = ['$filter', 'DataService'];