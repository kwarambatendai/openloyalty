export default class SegmentService {
    constructor(Restangular, EditableMap) {
        this.Restangular = Restangular;
        this.EditableMap = EditableMap;
    }

    getSegments(params) {
        if(!params) {
            params = {}
        }
        return this.Restangular.all('segment').getList(params);
    }

    getActiveSegments(params) {
        if(!params) {
            params = {}
        }
        params.active = true;

        return this.Restangular.all('segment').getList(params);
    }

    getSegmentCustomers(params, segmentId) {
        if(!params) {
            params = {}
        }
        return this.Restangular.one('segment', segmentId).all('customers').getList(params);
    }

    getSegment(segmentId) {
        return this.Restangular.one('segment', segmentId).get();
    }
    getFile(segmentId) {
        return this.Restangular.setFullResponse(true).one('csv').one('segment', segmentId).get();
    }

    postSegment(newSegment) {
        let self = this;

        return self.Restangular.one('segment').customPOST(
            {
                segment: self.EditableMap.segment(self.Restangular.stripRestangular(newSegment))
            }
        );
    }

    putSegment(segmentId, editedSegment) {
        let self = this;

        return self.Restangular.one('segment', segmentId).customPUT(
            {
                segment: self.Restangular.stripRestangular(self.EditableMap.segment(editedSegment))
            }
        );
    }

    postActivateSegment(segmentId) {
        return this.Restangular.one('segment').one(segmentId).one('activate').post();
    }

    postDeactivateSegment(segmentId) {
        return this.Restangular.one('segment').one(segmentId).one('deactivate').post();
    }

    deleteSegment(segmentId) {
        return this.Restangular.one('segment').one(segmentId).remove();
    }

}

SegmentService.$inject = ['Restangular', 'EditableMap'];